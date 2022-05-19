<?php

namespace App\Http\Controllers;

use Service\Models\Admin\AdminUser;
use Service\Models\User;
use Service\Models\ReportLotteryCompressed;
use Service\Models\UserLoginLog;
use Carbon\Carbon;
use Cache;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function getIndex()
    {
        $data = [];
        $data['admin'] = AdminUser::select([
            'admin_users.id',
            'admin_users.usernick',
            'admin_users.username',
            'admin_users.is_locked',
            'admin_users.created_at',
            'admin_users.last_time',
            'admin_users.last_ip',
            'admin_users.google_key',
            DB::raw('(CASE admin_users.id
                        WHEN 1 THEN \'超级管理员\'
                        ELSE string_agg(admin_roles.name, \'、\')
                        END) as role_name')
        ])
            ->leftJoin('admin_user_has_role', 'admin_users.id', 'admin_user_has_role.user_id')
            ->leftJoin('admin_roles', 'admin_user_has_role.role_id', 'admin_roles.id')
            ->where('admin_users.id', auth()->id())->groupBy('admin_users.id')->first();
        return view('iframe', $data);
    }

    public function getDashboard()
    {
        $data['onlines'] = Cache::remember(
            'Report::onlines',
            5,  // 缓存 5 分钟
            function () {
                return number_format2(User::where('last_active', '>', Carbon::now()->subMinutes(5))->count());
            }
        );

        $data['today_active'] = Cache::remember(
            'Report::today_active',
            6,  // 缓存 6 分钟
            function () {
                return number_format2(User::where('last_active', '>', Carbon::today())->count());
            }
        );

        $data['lottery_projects_today_users'] = Cache::remember(
            'Report::lottery_projects_today_users',
            60,  // 缓存 60 分钟
            function () {
                return number_format2(ReportLotteryCompressed::where('created_at', '>', Carbon::today())->distinct()->count('user_id'));
            }
        );

        $data['today_created'] = Cache::remember(
            'Report::today_created',
            10,  // 缓存 10 分钟
            function () {
                return number_format2(User::where('created_at', '>', Carbon::today())->count());
            }
        );

        $data['recent_most_visited_url'] = Cache::remember(
            'Report::recent_most_visited_url',
            1440,  // 缓存 24 小时
            function () {
                $data = UserLoginLog::where('created_at', '>', Carbon::today()->subDays(7))
                    ->groupBy('domain')
                    ->orderBy('count', 'desc')
                    ->take(6)
                    ->get([
                        'domain',
                        DB::raw('count(1) as count')
                    ]);

                $data = $data->mapWithKeys(function ($item) {
                    return [$item['domain'] => $item['count']];
                });

                return [
                    'keys' => json_encode($data->keys()),
                    'data' => json_encode($data->values())
                ];
            }
        );

        $data['recent_most_used_client'] = Cache::remember(
            'Report::recent_most_used_client',
            1441,  // 缓存 24 小时
            function () {
                $data = UserLoginLog::where('created_at', '>', Carbon::today()->subDays(7))
                    ->groupBy('browser')
                    ->orderBy('value', 'desc')
                    ->take(7)
                    ->get([
                        'browser as name',
                        DB::raw('count(1) as value')
                    ]);

                return json_encode($data);
            }
        );

        $data['recent_most_province_visits'] = Cache::remember(
            'Report::recent_most_province_visits',
            1442,  // 缓存 24 小时
            function () {
                $data = UserLoginLog::where('created_at', '>', Carbon::today()->subDays(7))
                    ->where('province', '>', '')
                    ->groupBy('province')
                    ->orderBy('value', 'desc')
                    ->get([
                        'province as name',
                        DB::raw('count(1) as value')
                    ]);

                return json_encode($data);
            }
        );

        return view('index', $data);
    }

    /**
     * 获取需要处理的问题，未开号 ，没有奖期等提醒
     */
    public function getTasks()
    {
        //需要生成奖期的彩种
        $tasks_issue = Cache::remember(
            "TasksWaringIssue",
            1, // 仅仅缓存 1 分钟
            function () {
                $latest_issues = DB::table('issue')
                    ->select('lottery_id', DB::raw('MAX(id) as id'))
                    ->where('sale_start', '>', Carbon::now())
                    ->groupBy('lottery_id');
                $miss_issue_lotteries = DB::table('lottery')
                    ->select('lottery.name', 'issue.belong_date')
                    ->leftJoinSub($latest_issues, 'latest_issue', function ($join) {
                        $join->on('lottery.id', '=', 'latest_issue.lottery_id');
                    })->leftJoin('issue', 'issue.id', 'latest_issue.id')
                    ->where('lottery.status', true)
                    ->where('lottery.special', '<', 2)
                    ->whereNotIn('lottery.ident', ['jndkl8', 'jndpcdd'])
                    ->get();

                $tasks = [];
                $two_days_date = Carbon::today()->addDay(2)->format('Y-m-d');
                foreach ($miss_issue_lotteries as $l) {
                    if (empty($l->belong_date) || $l->belong_date < $two_days_date) {
                        $tasks[] = $l->name . ' 需要生成奖期';
                    }
                }
                return $tasks;
            }
        );

        //抓不到号的彩种
        $tasks_draw = Cache::remember(
            "TasksWaringDraw",
            1 / 6, // 仅仅缓存 10 秒
            function () {
                //抓不到号的彩种--香港六合彩
                $latest_xglhc_issues = DB::table('issue')
                    ->select('issue.lottery_id', DB::raw('MIN(issue.id) as id'))
                    ->join('lottery', 'lottery.id', '=', 'issue.lottery_id')
                    ->where('lottery.ident', 'xglhc')
                    ->where('issue.earliest_write_time', '<', Carbon::now()->subMinutes(10))
                    ->where('issue.code_status', 0)
                    ->groupBy('issue.lottery_id');

                $latest_unopen_issues = DB::table('issue')
                    ->select('lottery_id', DB::raw('MIN(id) as id'))
                    ->where('earliest_write_time', '<', Carbon::now())
                    ->where('code_status', 0)
                    ->groupBy('lottery_id')
                    ->havingRaw("count(*) > 1")
                    ->union($latest_xglhc_issues);


                $upopen_issue_lotteries = DB::table('lottery')
                    ->select('lottery.name', 'issue.issue')
                    ->joinSub($latest_unopen_issues, 'latest_issue', function ($join) {
                        $join->on('lottery.id', '=', 'latest_issue.lottery_id');
                    })->leftJoin('issue', 'issue.id', 'latest_issue.id')
                    ->where('lottery.status', true)
                    ->where('lottery.special', '<', 2)
                    ->get();

                $tasks = [];
                foreach ($upopen_issue_lotteries as $l) {
                    $tasks[] = $l->name . " 第{$l->issue}期 抓取号码失败";
                }
                return $tasks;
            }
        );

        $tasks = array_merge($tasks_issue, $tasks_draw);
        return json_encode($tasks);
    }
}

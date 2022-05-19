<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LoginlogIndexRequest;
use Service\API\KillCode;
use Service\Models\Issue;
use Service\Models\KillCodeLog as ModelKillCodeLog;
use Service\Models\ReportLotteryCompressed as ModelReportLotteryCompressed;

class KillcodelogController extends Controller
{
    public function getIndex()
    {
        $data['start_date'] = Carbon::now()->format('Y-m-d 00:00:00');
        $data['end_date'] = Carbon::now()->format('Y-m-d 23:59:59');

        return view('kill-code-log.index', $data);
    }

    public function postIndex(LoginlogIndexRequest $request)
    {
        if ($request->ajax() || $request->get('export', 0)) {
            $data = [];
            $param = [];
            $param['start_date'] = (string)$request->get('start_date'); // 开始时间
            $param['end_date'] = (string)$request->get('end_date');     // 结束时间
            $param['third_ident'] = (string)$request->get('third_ident', ''); //第三方标识
            $param['third_lottery'] = (string)$request->get('third_lottery', '');//第三方彩种
            $param['third_issue'] = (string)$request->get('third_issue', '');//第三方奖期
            $param['local_lottery'] = (string)$request->get('local_lottery', '');//第三方奖期
            $param['local_issue'] = (string)$request->get('local_issue', '');//第三方奖期
            $param['flag_switch'] = (int)$request->get('flag_switch', -1);             //0无1有
            $param['error'] = (int)$request->get('error', -1);             // 报错
            $param['step'] = (int)$request->get('step', -1);             // 报错

            $start = $request->get('start');
            $length = $request->get('length');

            $model = ModelKillCodeLog::class;

            $where = function ($query) use ($param) {
                if (!empty($param['start_date'])) {
                    $query->where("created_at", '>=', $param['start_date']);
                }
                if (!empty($param['end_date'])) {
                    $query->where("created_at", '<=', $param['end_date']);
                }
                if (!empty($param['third_ident'])) {
                    $query->where("third_ident", $param['third_ident']);
                }
                if (!empty($param['third_lottery'])) {
                    $query->whereIn("third_lottery", explode(',', $param['third_lottery']));
                }
                if (!empty($param['third_issue'])) {
                    $query->whereIn("third_issue", explode(',', $param['third_issue']));
                }
                if (!empty($param['local_lottery'])) {
                    $query->whereIn("local_lottery", explode(',', $param['local_lottery']));
                }
                if (!empty($param['local_issue'])) {
                    $query->whereIn("local_issue", explode(',', $param['local_issue']));
                }

                if ($param['flag_switch'] >= 0) {
                    $query->where("flag_switch", $param['flag_switch']);
                }
                if ($param['error'] >= 0) {
                    $query->where("error", $param['error']);
                }
                if ($param['step'] >= 0) {
                    $query->where("step", $param['step']);
                }
                if (!empty($param['mode'])) {
                    $query->where("mode", $param['mode']);
                }
            };

            // 计算过滤后总数
            $count_query = $model::where($where);
            $count_sql = vsprintf(str_replace(array('?'), array('\'\'%s\'\''), $count_query->select([DB::raw(1)])->toSql()), $count_query->getBindings());
            $count = DB::selectOne("
                select count_estimate('{$count_sql}') as total
            ");

            if ($count->total > 20000) {
                $data['recordsTotal'] = $data['recordsFiltered'] = $count->total;
            } else {
                $data['recordsTotal'] = $data['recordsFiltered'] = $count_query->count();
            }

            $query_b = $model::where($where);
            $data['sum'] = $query_b->sum('all_bet_sum');
            $data['data'] = $query_b->select([
                "id",
                "third_ident",
                "third_lottery",
                "third_issue",
                "third_serial",
                "all_bet_sum",
                "all_bet_count",
                "flag_switch",
                "mode",
                "error",
                "step",
                "created_at",
                "calculated_at",
                "posted_at",
            ])->skip($start)->take($length)
                ->orderBy("id", 'desc')
                ->get();
            return response()->json($data);
        }
    }

    public function getDetail(Request $request)
    {
        $id = (int)$request->get('id');
        $log = ModelKillCodeLog::leftJoin('lottery', 'lottery.ident', 'kill_code_log.local_lottery')
            ->leftJoin('issue', function ($leftJoin) {
                $leftJoin->on('issue.issue', '=', 'kill_code_log.local_issue')
                    ->on('issue.lottery_id', '=', 'lottery.id');
            })
            ->where('kill_code_log.id', $id)
            ->select([
                'kill_code_log.*',
                'issue.code as i_code',
                'issue.code_status as i_code_status',
                'issue.bonus_status as i_bonus_status',
                'issue.report_status as i_report_status',
                'lottery.id as lottery_id', 'lottery.name as l_name',
            ])
            ->first()->toArray();
        $log['message'] = $log['message'] ? implode('<br>', array_filter(explode('|||', $log['message']))) : '';
        $log['code_map'] = $log['code_map'] ? json_decode($log['code_map'], true) : [];
        $log['real_sell'] = 0;//实际投注额
        $log['real_bonus'] = 0;//实际资金
        $log['real_profit'] = 0;//实际盈亏
        $log['real_plan_profit_diff'] = 0;//预测与实际的差别
        if ($log['i_report_status'] == 5 && $log['i_code']) {
            $report_lottery_compressed_sum = ModelReportLotteryCompressed::select([
                DB::raw('sum(price) as sum_sell'),
                DB::raw('sum(bonus) as sum_bonus'),
            ])->leftJoin('users', 'users.id', 'report_lottery_compressed.user_id')
                ->where('lottery_id', $log['lottery_id'])
                ->where('issue', $log['local_issue'])
                ->where('users.user_group_id', 1)
                ->groupBy(['lottery_id', 'issue'])
                ->first();
            $log['real_sell'] = $report_lottery_compressed_sum['sum_sell'];
            $log['real_bonus'] = $report_lottery_compressed_sum['sum_bonus'];
            $log['real_profit'] = $log['real_sell'] - $log['real_bonus'];

            $log['third_code'] = implode(',', str_split($log['i_code']));
            $log['plan_profit'] = ($log['code_map'] && isset($log['code_map'][$log['third_code']])) ? $log['code_map'][$log['third_code']] : 0;
            $log['real_plan_profit_diff'] = round_down($log['plan_profit'] - $log['real_profit'], 3);
        }

        //如果是分分彩合并了5分，10分的在此处汇入一起到view显示
        $need_merge_data = $this->_need_merge_issue_lottery($log);
        if ($need_merge_data) {
            $log['need_merge_data'] = $need_merge_data['need_merge_data'];
            $log['total_real_sell'] = round_down($log['real_sell'] + $need_merge_data['total']['merge_real_sell'], 3);
            $log['total_real_bonus'] = round_down($log['real_bonus'] + $need_merge_data['total']['merge_real_bonus'], 3);
            $log['total_real_profit'] = round_down($log['real_profit'] + $need_merge_data['total']['merge_real_profit']);
            $log['real_plan_profit_diff'] = round_down($log['real_plan_profit_diff'] - $need_merge_data['total']['merge_real_profit'], 3);
        }

        return view('kill-code-log.detail', $log);
    }

    protected function _need_merge_issue_lottery($log)
    {
        //分分彩需要合并的5分，10分彩
        $need_merge_data = (new KillCode)->mergeLotteryIssue($log['local_lottery'], $log['local_issue']);
        $_need_merge_lottery = $need_merge_data['need_merge_lottery'];
        $_need_merge_issue = $need_merge_data['need_merge_issue'];
        if (!$_need_merge_lottery || !$_need_merge_issue) {
            return $_need_merge_lottery;
        }

        $need_merge_data = Issue::leftJoin('lottery', 'lottery.id', 'issue.lottery_id')
            ->whereIn('lottery_id', function ($query) use ($_need_merge_lottery) {
                $query->select('id')->from('lottery')->whereIn('ident', $_need_merge_lottery);
            })->whereIn('issue', $_need_merge_issue)
            ->where(DB::RAW("date_trunc('minutes', sale_end)"), '=', date("Y-m-d H:i:00", strtotime($log['local_issue_sale_end'])))
            ->select([
                'lottery.name as l_name',
                'lottery.ident as l_ident',
                'issue as l_issue',
                'lottery_id',
                'sale_start as l_sale_start',
                'sale_end as l_sale_end',
                'code as i_code',
                'code_status as i_code_status',
                'bonus_status as i_bonus_status',
                'report_status as i_report_status',
            ])
            ->orderBy('l_issue', 'desc')
            ->get()
            ->toArray();

        $merge_real_profit = 0;//盈亏偏差累计值
        $merge_real_sell = 0;//注金累计值
        $merge_real_bonus = 0;//奖金累计值
        foreach ($need_merge_data as $need_merge_data_key => $need_merge_data_value) {
            $need_merge_data[$need_merge_data_key]['real_sell'] = 0;//注金
            $need_merge_data[$need_merge_data_key]['real_bonus'] = 0;//奖金
            $need_merge_data[$need_merge_data_key]['real_profit'] = 0;//盈亏
            if ($need_merge_data_value['i_report_status'] == 5 && $need_merge_data_value['i_code']) {
                DB::enableQueryLog();
                $report_lottery_compressed_sum = ModelReportLotteryCompressed::select([
                    DB::raw('sum(price) as sum_sell'),
                    DB::raw('sum(bonus) as sum_bonus'),
                ])->leftJoin('users', 'users.id', 'report_lottery_compressed.user_id')
                    ->where('lottery_id', $need_merge_data_value['lottery_id'])
                    ->where('issue', $need_merge_data_value['l_issue'])
                    ->where('users.user_group_id', 1)
                    ->groupBy(['lottery_id', 'issue'])
                    ->first();
                $need_merge_data[$need_merge_data_key]['real_sell'] = $report_lottery_compressed_sum['sum_sell'];
                $need_merge_data[$need_merge_data_key]['real_bonus'] = $report_lottery_compressed_sum['sum_bonus'];
                $need_merge_data[$need_merge_data_key]['real_profit'] = $report_lottery_compressed_sum['sum_sell'] - $report_lottery_compressed_sum['sum_bonus'];
                $merge_real_profit += $need_merge_data[$need_merge_data_key]['real_profit'];
                $merge_real_sell += $need_merge_data[$need_merge_data_key]['real_sell'];
                $merge_real_bonus += $need_merge_data[$need_merge_data_key]['real_bonus'];
            }
        }

        return [
            'need_merge_data' => $need_merge_data,
            'total' => [
                'merge_real_profit' => $merge_real_profit,//盈亏累计值
                'merge_real_sell' => $merge_real_sell, //注金累计值
                'merge_real_bonus' => $merge_real_bonus, //奖金累计值
            ]
        ];

    }
}

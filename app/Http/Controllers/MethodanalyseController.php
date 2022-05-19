<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Service\API\Lottery as APILottery;
use Service\Models\MethodAnalyse;
use Service\Models\MethodAnalyseTotal;
use App\Http\Requests\MethodAnalyseRequest;
use Illuminate\Support\Facades\DB;

class MethodanalyseController extends Controller
{
    public function getIndex(Request $request)
    {
        $data['start_date'] = $data['end_date'] = Carbon::today()->toDateString();
        $data['lottery_list'] = APILottery::getAllLotteryGroupByCategory();
        $view = $request->get('mode') == 'user' ? 'user' : 'platform';

        return view('method-analyse/' . $view, $data);
    }

    public function postIndex(MethodAnalyseRequest $request)
    {
        $start = $request->get('start');
        $length = $request->get('length');
        $order = $request->get('order');
        $columns = $request->get('columns');
        $param['start_date'] = $request->get('start_date');     //开始日期
        $param['end_date'] = $request->get('end_date');         //结束日期
        $param['lottery_id'] = $request->get('lottery_id');     //彩种ID
        $param['username'] = $request->get('username');         //用户名

        if ($request->get('mode') == 'user') {
            if ($param['username']) {
                $row = \Service\Models\User::where('username', $param['username'])->first();
                $param['user_id'] = $row ? $row->id : 0;
            }

            if ($param['lottery_id']) {
                $where = function ($query) use ($param) {
                    $query->whereBetween('belong_date', [$param['start_date'], $param['end_date']]);
                    $query->where('lottery_id', $param['lottery_id']);
                    if (isset($param['user_id'])) {
                        $query->where('user_id', $param['user_id']);
                    }
                };

                $table = MethodAnalyse::where($where);
                $data['data'] = DB::table(DB::raw("({$table->toSql()}) as sub"))->select([
                    'sub.*',
                    'users.username',
                    'lottery.name as lottery_name',
                    DB::raw('(plm.name || \' - \' || lm.name) as method_name'),
                    DB::raw('(sub.price - sub.rebate) as real_price'),
                    DB::raw('(sub.price - sub.rebate - sub.bonus) as profit'),
                    DB::raw('coalesce(trunc(sub.win_count::numeric / nullif(sub.bet_count, 0) * 100), 0) as win_percent'),
                    DB::raw('round(sub.bonus / (sub.price - sub.rebate),4) as rtp'),
                ])->leftJoin('users', 'users.id', 'sub.user_id')
                    ->leftJoin('lottery', 'lottery.id', 'sub.lottery_id')
                    ->leftJoin('lottery_method as lm', 'lm.id', 'sub.lottery_method_id')
                    ->leftJoin('lottery_method as plm', 'lm.parent_id', 'plm.id');
            } else {
                $where = function ($query) use ($param) {
                    $query->whereBetween('belong_date', [$param['start_date'], $param['end_date']]);
                    if (isset($param['user_id'])) {
                        $query->where('user_id', $param['user_id']);
                    }
                };

                $table = MethodAnalyse::select([
                    'belong_date',
                    'lottery_id',
                    'user_id',
                    DB::raw('sum(bet_count) as bet_count'),
                    DB::raw('sum(win_count) as win_count'),
                    DB::raw('sum(price) as price'),
                    DB::raw('sum(bonus) as bonus'),
                    DB::raw('sum(rebate) as rebate'),
                ])->where($where)->groupBy(['belong_date', 'lottery_id', 'user_id']);

                $data['data'] = DB::table(DB::raw("({$table->toSql()}) as sub"))->select([
                    'sub.*',
                    'users.username',
                    'lottery.name as lottery_name',
                    DB::raw('\' - \' as method_name'),
                    DB::raw('(sub.price - sub.rebate) as real_price'),
                    DB::raw('(sub.price - sub.rebate - sub.bonus) as profit'),
                    DB::raw('coalesce(trunc(sub.win_count::numeric / nullif(sub.bet_count, 0) * 100), 0) as win_percent'),
                    DB::raw('round(sub.bonus / (sub.price - sub.rebate),4) as rtp'),
                ])->leftJoin('users', 'users.id', 'sub.user_id')
                    ->leftJoin('lottery', 'lottery.id', 'sub.lottery_id');
            }
        } else {
            if ($param['lottery_id']) {
                $where = function ($query) use ($param) {
                    $query->whereBetween('belong_date', [$param['start_date'], $param['end_date']]);
                    $query->where('lottery_id', $param['lottery_id']);
                };

                $table = MethodAnalyseTotal::where($where);

                $data['data'] = DB::table(DB::raw("({$table->toSql()}) as sub"))->select([
                    'sub.*',
                    'lottery.name as lottery_name',
                    DB::raw('(plm.name || \' - \' || lm.name) as method_name'),
                    DB::raw('(sub.price - sub.rebate) as real_price'),
                    DB::raw('(sub.price - sub.rebate - sub.bonus) as profit'),
                    DB::raw('coalesce(trunc(sub.win_count::numeric / nullif(sub.bet_count, 0) * 100), 0) as win_percent'),
                    DB::raw('round(sub.bonus / (sub.price - sub.rebate),4) as rtp'),
                ])->leftJoin('lottery', 'lottery.id', 'sub.lottery_id')
                    ->leftJoin('lottery_method as lm', 'lm.id', 'sub.lottery_method_id')
                    ->leftJoin('lottery_method as plm', 'lm.parent_id', 'plm.id');
            } else {
                $where = function ($query) use ($param) {
                    $query->whereBetween('belong_date', [$param['start_date'], $param['end_date']]);
                };

                $table = MethodAnalyseTotal::select([
                    'belong_date',
                    'lottery_id',
                    DB::raw('min(lottery_bet_user) as bet_user'),
                    DB::raw('sum(bet_count) as bet_count'),
                    DB::raw('sum(win_count) as win_count'),
                    DB::raw('sum(price) as price'),
                    DB::raw('sum(bonus) as bonus'),
                    DB::raw('sum(rebate) as rebate'),
                ])->where($where)->groupBy(['belong_date', 'lottery_id']);

                $data['data'] = DB::table(DB::raw("({$table->toSql()}) as sub"))->select([
                    'sub.*',
                    'lottery.name as lottery_name',
                    DB::raw('\' - \' as method_name'),
                    DB::raw('(sub.price - sub.rebate) as real_price'),
                    DB::raw('(sub.price - sub.rebate - sub.bonus) as profit'),
                    DB::raw('coalesce(trunc(sub.win_count::numeric / nullif(sub.bet_count, 0) * 100), 0) as win_percent'),
                    DB::raw('round(sub.bonus / (sub.price - sub.rebate),4) as rtp'),
                ])->leftJoin('lottery', 'lottery.id', 'sub.lottery_id');
            }
        }

        $data['recordsTotal'] = $data['recordsFiltered'] = DB::table(DB::raw("({$table->toSql()}) as sub"))
            ->mergeBindings($table->getQuery())
            ->count();

        $data['data'] = $data['data']->mergeBindings($table->getQuery())
            ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
            ->skip($start)->take($length)
            ->get();

        return response()->json($data);
    }
}

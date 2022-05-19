<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Http\Requests\ThirdGameUserProfitRequest;
use Service\Models\User;
use Service\Models\ReportThirdGameUserProfit;
use Illuminate\Support\Facades\DB;
use Service\API\ThirdGame\ThirdGame as ApiThirdGame;

class ThirdgameuserprofitController extends Controller
{
    public function getIndex()
    {
        $yesterday = Carbon::yesterday()->toDateString();
        $platforms = ApiThirdGame::getPlatform('', 'array', 0);
        return view('third-game-user-profit.index', [
            'platforms' => $platforms,
            'start_date' => $yesterday,
            'end_date' => $yesterday,
        ]);
    }

    public function postIndex(ThirdGameUserProfitRequest $request)
    {
        $data = ['recordsTotal' => 0, 'data' => []];
        $param['columns'] = $request->get('columns');
        $param['start_date'] = $request->get('start_date');
        $param['end_date'] = $request->get('end_date');
        $param['platform_id'] = $request->get('platform_id');
        $param['username'] = $request->get('username');
        $param['include_all'] = $request->get('include_all');
        $param['amount_min'] = $request->get('amount_min');
        $param['amount_max'] = $request->get('amount_max');
        $param['user_id'] = 0;
        $order = $request->get('order');
        $start = $request->get('start');
        $length = $request->get('length');
        if ($param['username']) {
            $user = User::select('id')->where('username', $param['username'])->first();
            if (!$user) {
                return response()->json($data);
            }
            $param['user_id'] = $user->id;
        }
        $where = function ($query) use ($param) {
            $query->whereBetween('report_third_game_user_profit.date', [$param['start_date'], $param['end_date']]);
            if ($param['platform_id']) {
                $query->where("report_third_game_user_profit.platform_id", $param['platform_id']);
            }
            if ($param['user_id'] && empty($param['include_all'])) {
                $query->where("report_third_game_user_profit.user_id", $param['user_id']);
            }
            if ($param['user_id'] && $param['include_all']) {
                $query->where(function ($query) use ($param) {
                    $query->where('users.id', $param['user_id'])
                        ->orWhere('users.parent_tree', '@>', $param['user_id']);
                });
            }
            if ($param['amount_min']) {
                $query->where(DB::raw("(report_third_game_user_profit.data->>'bet')::numeric"), ">=", $param['amount_min']);
            }
            if ($param['amount_max']) {
                $query->where(DB::raw("(report_third_game_user_profit.data->>'bet')::numeric"), "<=", $param['amount_max']);
            }
        };
        $data['recordsTotal'] = $data['recordsFiltered'] = ReportThirdGameUserProfit::leftJoin('users', 'users.id', 'report_third_game_user_profit.user_id')->where($where)->count();
        $data['data'] = ReportThirdGameUserProfit::select([
            'report_third_game_user_profit.date',
            'users.username',
            'third_game_platform.name',
            DB::raw("report_third_game_user_profit.data->>'bet' as bet"),                   //个人销量
            DB::raw("report_third_game_user_profit.data->>'admin_deduct' as admin_deduct"), //管理员扣减
            DB::raw("report_third_game_user_profit.data->>'win' as win"),                   //平台盈亏
            DB::raw("report_third_game_user_profit.data->>'fd' as fd"),                     //返水
            DB::raw("report_third_game_user_profit.data->>'ds' as ds"),                     //打赏
            DB::raw("report_third_game_user_profit.data->>'chou_shui' as chou_shui"),       //抽水
        ])->leftJoin('third_game_platform', 'third_game_platform.id', 'report_third_game_user_profit.platform_id')
            ->leftJoin('users', 'users.id', 'report_third_game_user_profit.user_id')
            ->where($where)
            ->skip($start)->take($length)
            ->orderBy($param['columns'][$order[0]['column']]['data'], $order[0]['dir'])
            ->get();
        foreach ($data['data'] as $key => $val) {
            $data['data'][$key]['user_win'] = $data['data'][$key]['bet'] - $data['data'][$key]['win']; //个人派奖
            $data['data'][$key]['win'] += $data['data'][$key]['admin_deduct'];
            $data['data'][$key]['real_win'] = $data['data'][$key]['win'] - $data['data'][$key]['fd']; //最终结算[平台盈亏-返水]
        }
        return response()->json($data);
    }
}

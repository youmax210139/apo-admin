<?php

namespace App\Http\Controllers;

use App\Http\Requests\ThirdGameDailyRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Service\Models\ReportDailyThirdGame;
use Service\API\ThirdGame\ThirdGame as ApiThirdGame;

class ThirdgamedailyController extends Controller
{
    public function getIndex()
    {
        $yesterday = Carbon::yesterday()->toDateString();
        return view(
            'third-game-daily-report/index',
            [
                'start_date' => $yesterday,
                'end_date' => $yesterday,
                'platforms' => ApiThirdGame::getPlatform('', 'array', 0),
            ]
        );
    }

    public function postIndex(ThirdGameDailyRequest $request)
    {
        $start = $request->get('start');
        $length = $request->get('length');
        $columns = $request->get('columns');
        $order = $request->get('order');

        $param['start_date'] = $request->get('start_date');         //开始时间
        $param['end_date'] = $request->get('end_date');             //结束时间
        $param['platform_id'] = $request->get('platform_id');       //所属游戏平台ID

        $where = function ($query) use ($param) {
            $query->whereBetween('report_daily_third_game.date', [$param['start_date'], $param['end_date']]);
            $query->where("report_daily_third_game.platform_id", $param['platform_id']);
        };
        $data['recordsTotal'] = $data['recordsFiltered'] = ReportDailyThirdGame::query()->where($where)->count();

        $data['data'] = ReportDailyThirdGame::select([
            'report_daily_third_game.date',
            'third_game_platform.ident',
            'third_game_platform.name',
            DB::raw("report_daily_third_game.data->>'total_bets' as total_bets"),
            DB::raw("report_daily_third_game.data->>'platform_wins' as platform_wins"),
            DB::raw("report_daily_third_game.data->>'fd' as fd"),
            DB::raw("report_daily_third_game.data->>'ds' as ds"),
            DB::raw("report_daily_third_game.data->>'chou_shui' as chou_shui"),
            DB::raw("report_daily_third_game.data->>'admin_deduct' as admin_deduct")
        ])
            ->leftJoin('third_game_platform', 'third_game_platform.id', 'report_daily_third_game.platform_id')
            ->where($where)
            ->skip($start)
            ->take($length)
            ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
            ->get();

        if (!$data['data']->isEmpty()) {
            foreach ($data['data'] as $key => $val) {
                if ($data['data'][$key]['ident']) {
                    $data['data'][$key]['name'] = $data['data'][$key]['name'] . '[' . $data['data'][$key]['ident'] . ']';
                } else {
                    $data['data'][$key]['name'] = '全部[All]';
                }

                $data['data'][$key]['total_wins'] = $data['data'][$key]['total_bets'] - $data['data'][$key]['platform_wins'];
                $data['data'][$key]['platform_wins'] += $data['data'][$key]['admin_deduct'];
                $data['data'][$key]['real_win'] = $data['data'][$key]['platform_wins'] - $data['data'][$key]['fd'];
            }

            $data['sum_amount'] = ReportDailyThirdGame::select([
                DB::raw("sum((report_daily_third_game.data->>'total_bets')::numeric) as total_bets"),
                DB::raw("sum((report_daily_third_game.data->>'platform_wins')::numeric) as platform_wins"),
                DB::raw("sum((report_daily_third_game.data->>'fd')::numeric) as fd"),
                DB::raw("sum((report_daily_third_game.data->>'ds')::numeric) as ds"),
                DB::raw("sum((report_daily_third_game.data->>'chou_shui')::numeric) as chou_shui"),
                DB::raw("sum((report_daily_third_game.data->>'admin_deduct')::numeric) as admin_deduct")
            ])->where($where)->first();
            $data['sum_amount']['total_wins'] = $data['sum_amount']['total_bets'] - $data['sum_amount']['platform_wins'];
            $data['sum_amount']['platform_wins'] += $data['sum_amount']['admin_deduct'];
            $data['sum_amount']['real_win'] = $data['sum_amount']['platform_wins'] - $data['sum_amount']['fd'];
        }
        return response()->json($data);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ThirdGameBetIndexRequest;
use Service\API\ThirdGame\Report\Creator;
use Service\API\ThirdGame\ThirdGame as ApiThirdGame;
use Service\Models\User;

class ThirdgamebetController extends Controller
{
    public function getIndex()
    {
        $platforms = ApiThirdGame::getPlatform('', 'array', 0);
        return view('third-game-bet.index', [
            'platforms' => $platforms,
            'start_date' => date('Y-m-d 00:00:00', strtotime("-2 days")),
        ]);
    }

    public function postIndex(ThirdGameBetIndexRequest $request)
    {
        if ($request->ajax()) {
            $data = ['recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []];
            $start = $request->get('start');
            $length = $request->get('length');

            $param['order'] = $request->get('order');
            $param['columns'] = $request->get('columns');
            $param['id'] = (int)trim($request->get('id'));
            $param['bet_id'] = trim($request->get('bet_id'));
            $param['username'] = trim($request->get('username'));
            $param['start_date'] = $request->get('start_date');
            $param['end_date'] = $request->get('end_date');
            $param['platform'] = $request->get('platform');
            $param['amount_min'] = $request->get('amount_min');
            $param['amount_max'] = $request->get('amount_max');
            $param['order'] = $request->get('order');
            $param['desc'] = $request->get('desc');
            $param['user_id'] = 0;
            $param['order_by'] = $param['columns'][$param['order'][0]['column']]['data'];
            $param['order_dir'] = $param['order'][0]['dir'];
            if ($param['username']) {
                $user = User::select('id')->where('username', $param['username'])->first();
                if (!$user) {
                    return response()->json($data);
                }
                $param['user_id'] = $user->id;
            }
            if ($param['platform']) {
                $data = Creator::factory($param['platform'])->getBetList($param, $start, $length);
            }

            return response()->json($data);
        }
    }
}

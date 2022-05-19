<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Service\API\ThirdGame\Platform\Creator;
use Service\API\ThirdGame\ThirdGame;
use Service\Models\ThirdGameUser as ModelThirdUser;
use App\Http\Requests\ThirdGameUserIndexRequest;
use Service\Models\ThirdGameUser;

class ThirdgameuserController extends Controller
{
    public function getIndex()
    {
        $platforms = ThirdGame::getThirdGame('', null, 'array', 0);
        return view('third-game-user.index', [
            'platforms' => $platforms,
            'start_date' => date('Y-m-d 00:00:00', strtotime("-2 days")),
        ]);
    }

    public function postIndex(ThirdGameUserIndexRequest $request)
    {
        if ($request->ajax() || true) {
            $start = $request->get('start');
            $length = $request->get('length');

            $param['username'] = trim($request->get('username'));
            $param['created_start_date'] = $request->get('created_start_date');
            $param['created_end_date'] = $request->get('created_end_date');
            $param['platform'] = $request->get('platform');
            $param['order'] = $request->get('order');
            $param['desc'] = $request->get('desc');
            $param['is_search'] = $request->get('is_search');

            $data = array();
            $where = array();
            if ($param['username']) {
                $where[] = ['u.username', '=', $param['username']];
            }
            if ($param['created_start_date']) {
                $where[] = ['third_game_user.created_at', '>=', $param['created_start_date']];
            }
            if ($param['created_end_date']) {
                $where[] = ['third_game_user.created_at', '<=', $param['created_end_date']];
            }
            if ($param['platform']) {
                $where[] = ['tg.ident', '=', $param['platform']];
            }

            $model = ModelThirdUser::select([
                'u.username',
                'user_profile.value as user_observe',
                'third_game_user.*',
                'tg.name AS third_game_name'
            ])
                ->leftJoin('users AS u', 'u.id', 'third_game_user.user_id')
                ->leftJoin('user_profile', function ($join) {
                    $join->on('user_profile.user_id', '=', 'u.id')
                        ->where('user_profile.attribute', 'user_observe');
                })
                ->leftJoin('third_game AS tg', 'tg.id', 'third_game_user.third_game_id')
                ->where($where);

            $total = $model->count();
            $data['recordsTotal'] = $data['recordsFiltered'] = $total;
            $data['data'] = $model->skip($start)->take($length)->get();
            $data['data'] = json_decode(json_encode($data['data']), true);

            return response()->json($data);
        }
    }

    public function postLock(Request $request)
    {
        $id = $request->post('id');
        $row = ModelThirdUser::find($id);

        if ($row) {
            $row->is_lock = $row->is_lock ? 0 : 1;
            $row->save();
            return response()->json(['status' => 0]);
        } else {
            return response()->json(['status' => 1, 'msg' => '找不到记录']);
        }
    }

    /**
     * 获取第三方余额
     * @param Request $request
     * @return string
     */
    public function postRefresh(Request $request)
    {
        $id = $request->get('id');

        $request->validate([
            'id' => 'required|int',
        ]);

        $user = ThirdGameUser::select('third_game_user.user_id', 'third_game.ident')
            ->leftJoin('third_game', 'third_game_user.third_game_id', 'third_game.id')
            ->where('third_game_user.id', $id)
            ->first();
        if (!$user) {
            return response()->json(['status' => 1, 'msg' => '找不到记录']);
        }
        $platform = Creator::factory($user->ident);
        if (!$platform) {
            return response()->json(['status' => 1, 'msg' => '非预期的平台']);
        }

        $balance = $platform->getUserBalance($user->user_id, true);

        return response()->json(['balance' => $balance && $balance != '--' ? (float)$balance : 0, 'msg' => '', 'status' => 0]);
    }
}

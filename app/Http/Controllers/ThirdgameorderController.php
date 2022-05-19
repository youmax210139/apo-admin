<?php

namespace App\Http\Controllers;

use App\Http\Requests\ThirdGameOrderIndexRequest;
use Illuminate\Support\Facades\DB;
use Service\API\ThirdGame\ThirdGame as ApiThirdGame;
use Service\Models\ThirdGameOrder as ModelThirdOrder;

class ThirdgameorderController extends Controller
{
    public function getIndex()
    {
        $platforms = ApiThirdGame::getThirdGame('', null);
        return view('third-game-order.index', [
            'platforms' => $platforms,
            'start_date' => date('Y-m-d 00:00:00', strtotime("-2 days")),
        ]);
    }

    public function postIndex(ThirdGameOrderIndexRequest $request)
    {
        if ($request->ajax() || true) {
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');

            $param['order_num'] = trim($request->get('order_num'));
            $param['username'] = trim($request->get('username'));
            $param['created_start_date'] = $request->get('created_start_date');
            $param['created_end_date'] = $request->get('created_end_date');
            $param['from'] = $request->get('from');
            $param['to'] = $request->get('to');
            $param['amount_min'] = $request->get('amount_min');
            $param['amount_max'] = $request->get('amount_max');
            $param['status'] = $request->get('status');

            $param['order'] = $request->get('order');
            $param['desc'] = $request->get('desc');
            $param['is_search'] = $request->get('is_search');

            $data = array();
            $where = array();
            if ($param['order_num']) {
                $where[] = ['third_game_order.order_num', '=', $param['order_num']];
            }
            if ($param['username']) {
                $where[] = ['users.username', '=', $param['username']];
            }
            if ($param['from']) {
                if ($param['from'] == 'Master') {
                    $where[] = ['third_game_order.from', '=', 0];
                } else {
                    $where[] = ['tgf.ident', '=', $param['from']];
                }
            }
            if ($param['to']) {
                if ($param['to'] == 'Master') {
                    $where[] = ['third_game_order.to', '=', 0];
                } else {
                    $where[] = ['tgt.ident', '=', $param['to']];
                }
            }
            if ($param['status']) {
                if ($param['status'] == 1) {
                    $where[] = ['third_game_order.status', '=', 1];
                } else {
                    $where[] = ['third_game_order.status', '>', 1];
                }
            }
            if ($param['created_start_date']) {
                $where[] = ['third_game_order.created_at', '>=', $param['created_start_date']];
            }
            if ($param['created_end_date']) {
                $where[] = ['third_game_order.created_at', '<=', $param['created_end_date']];
            }
            if ($param['amount_min']) {
                $where[] = ['third_game_order.amount', '>=', $param['amount_min']];
            }
            if ($param['amount_max']) {
                $where[] = ['third_game_order.amount', '<=', $param['amount_max']];
            }


            switch ($columns[$order[0]['column']]['data']) {
                case 'username':
                    $order_field = 'third_game_order.username';
                    break;
                case 'amount':
                    $order_field = 'third_game_order.amount';
                    break;
                case 'created_at':
                    $order_field = 'third_game_order.created_at';
                    break;
                case 'sn':
                default:
                    $order_field = 'third_game_order.order_num';
            }

            $data['order_statuses'] = $order_statuses = ApiThirdGame::getOrderStatuses();
            $model = ModelThirdOrder::select([
                'third_game_order.*', 'users.username',
                'user_profile.value as user_observe',
                DB::raw("coalesce(tgf.ident, 'Master') AS from"), DB::raw("coalesce(tgt.ident, 'Master') AS to")
            ])
                ->leftJoin('third_game AS tgf', 'tgf.id', 'third_game_order.from')
                ->leftJoin('third_game AS tgt', 'tgt.id', 'third_game_order.to')
                ->leftJoin('users', 'users.id', 'third_game_order.user_id')
                ->leftJoin('user_profile', function ($join) {
                    $join->on('user_profile.user_id', '=', 'users.id')
                        ->where('user_profile.attribute', 'user_observe');
                })
                ->where($where);

            $total = $model->count();
            $data['recordsTotal'] = $data['recordsFiltered'] = $total;
            $data['data'] = $model->skip($start)->take($length)->orderBy($order_field, $order[0]['dir'])->get();
            foreach ($data['data'] as &$row) {
                $row->status_label = $order_statuses[$row->status];
            }
            $data['data'] = json_decode(json_encode($data['data']), true);
            return response()->json($data);
        }
    }
}

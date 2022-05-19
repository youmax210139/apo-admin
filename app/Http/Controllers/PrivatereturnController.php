<?php

namespace App\Http\Controllers;

use Service\Models\PrivateReturn as PrivateReturnModel;
use Service\Models\User as UserModel;
use Illuminate\Support\Facades\DB;
use Service\Models\UserGroup;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PrivatereturnController extends Controller
{
    public function getIndex()
    {
        $start_time = Carbon::yesterday()->format('Y-m-d 00:00:00');
        $end_time = Carbon::now()->format('Y-m-d 23:59:59');
        $user_group = UserGroup::all();
        return view(
            'private-return.index',
            [
                'user_group' => $user_group,
                'start_time' => $start_time,
                'end_time' => $end_time
            ]
        );
    }

    public function postIndex(Request $request)
    {
        if ($request->ajax()) {
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');

            $param['username'] = trim($request->get('username'));
            $param['source_user'] = trim($request->get('source_user'));
            $param['amount_min'] = $request->get('amount_min');
            $param['amount_max'] = $request->get('amount_max');
            $param['user_group_id'] = $request->get('user_group_id');
            $param['frozen'] = trim($request->get('frozen'));
            $param['search_scope'] = trim($request->get('search_scope'));
            $param['start_time'] = $request->get('start_time');
            $param['end_time'] = $request->get('end_time');
            $param['status'] = $request->get('status');

            $param['order'] = $request->get('order');
            $param['desc'] = $request->get('desc');

            $data = [];
            if ($param['username']) {
                $data['data'] = [];
                $search_user = UserModel::where('username', $param['username'])->first();
                if (!$search_user) {
                    return response()->json($data);
                }
            }
            if ($param['source_user']) {
                $data['data'] = [];
                $source_user = UserModel::where('username', $param['source_user'])->first();
                if (!$source_user) {
                    return response()->json($data);
                }
            }
            $where = function ($query) use ($param) {
                if ($param['frozen'] == '1') {
                    $query->where('users.frozen', '>', 0);
                } elseif ($param['frozen'] == '2') {
                    $query->where('users.frozen', '=', 0);
                }
                if ($param['status'] !== '') {
                    $query->where('private_return.status', '=', $param['status']);
                }
                if ($param['start_time']) {
                    $query->where('private_return.start_time', '>=', $param['start_time']);
                }
                if ($param['end_time']) {
                    $query->where('private_return.end_time', '<=', $param['end_time']);
                }
                if ($param['amount_min']) {
                    $query->where('private_return.amount', '>=', $param['amount_min']);
                }
                if ($param['amount_max']) {
                    $query->where('private_return.amount', '<=', $param['amount_max']);
                }
                if ($param['user_group_id']) {
                    $query->where('users.user_group_id', '=', $param['user_group_id']);
                }
            };

            $model = PrivateReturnModel::select([
                'private_return.*',
                'users.username as username',
                'users.parent_tree',
                'source.username as source_user',
                'user_group.name as user_group',
            ])
                ->leftJoin('users', 'users.id', 'private_return.user_id')
                ->leftJoin('users as source', 'source.id', 'private_return.source_user_id')
                ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
                ->where($where);

            if ($param['username']) {
                $model->where(function ($query) use ($param, $search_user) {
                    return $this->_usernameWhere($query, $param, $search_user);
                });
            }

            if ($param['source_user']) {
                $model->where(function ($query) use ($param) {
                    $query->where('source.username', '=', $param['source_user']);
                });
            }

            //合计
            $total_model = PrivateReturnModel::select([
                DB::raw('SUM(amount) as total_amount')
            ])
                ->leftJoin('users', 'users.id', 'private_return.user_id')
                ->where($where);
            if ($param['username']) {
                $total_model->where(function ($query) use ($param, $search_user) {
                    return $this->_usernameWhere($query, $param, $search_user);
                });
            }
            $data['totalSum'] = $total_model->first();


            $total = $model->count();
            $data['recordsTotal'] = $data['recordsFiltered'] = $total;
            $columns_orderby = ['username', 'parent_tree', 'amount'];
            if (in_array($columns[$order[0]['column']]['data'], $columns_orderby)) {
                $model->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir']);
            } else {
                $model->orderByRaw("(remark->>'{$columns[$order[0]['column']]['data']}')::FLOAT {$order[0]['dir']}");
            }
            $data['data'] = $model->skip($start)->take($length)->get()->toArray();
            foreach ($data['data'] as $_key => $_row) {

                $level = count(json_decode($_row['parent_tree'], true));
                $_row['parent_tree'] = $level ? "{$level}级代理" : "总代";
                $_row['status_label'] = __('private_return.status_' . $_row['status']);
                $remark = json_decode($_row['remark'], true);
                $data['data'][$_key] = array_merge($_row, $remark);
            }
            return response()->json($data);
        }
    }

    public function postDetail(Request $request)
    {
        $id = (int)$request->post('id', 0);
        $fields = [
            'private_return.*',
            'users.username',
            'user_group.name as user_group_name',
            'lottery.name as lottery_name',
            'source.username as source_name',
            'contract.time_type as time_type',
            'contract.condition_type',
            'contract.cardinal_type',
            DB::raw("
                    CASE
                        WHEN
                            users.user_type_id!=1
                        THEN
                            concat(jsonb_array_length(users.parent_tree),'级',user_type.name)
                        ELSE
                            user_type.name
                    END as user_type_name
                "),
        ];
        $detail = PrivateReturnModel::select($fields)
            ->leftJoin('users', 'users.id', 'private_return.user_id')
            ->leftJoin('user_type', 'user_type.id', 'users.user_type_id')
            ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
            ->leftJoin('lottery', 'lottery.id', 'private_return.lottery_id')
            ->leftJoin('users as source', 'source.id', 'private_return.source_user_id')
            ->leftJoin('user_private_return_contract as contract', 'contract.id', 'private_return.contract_id')
            ->where('private_return.id', $id)->first();

        $detail->time_type_label = __('private_return.time_type_' . $detail->time_type);
        $detail->condition_type_label = __('private_return.condition_type_' . $detail->condition_type);
        $detail->cardinal_type_label = __('private_return.cardinal_type_' . $detail->cardinal_type);
        $remarks = json_decode($detail->remark, true);
        if (count($remarks) > 0) {
            $detail->remark = $remarks;
        } else {
            $detail->remark = null;
        }

        return view('private-return.detail', [
            'detail' => $detail,
        ]);
    }

    private function _usernameWhere($query, $param, $search_user)
    {
        if ($param['search_scope'] == 'directly') {
            $query->where('users.parent_id', '=', $search_user->id);
            $query->orWhere('users.id', '=', $search_user->id);
        } elseif ($param['search_scope'] == 'team') {
            $query->where('users.parent_tree', '@>', $search_user->id);
            $query->orWhere('users.id', '=', $search_user->id);
        } else {
            $query->where('users.username', '=', $param['username']);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Service\Models\User;
use Illuminate\Http\Request;
use Service\API\Dividend\Dividend;
use Service\Models\ContractDividend;
use Service\Models\UserGroup;
use Service\Models\UserType;
use Illuminate\Support\Facades\DB;

class DividendreportController extends Controller
{
    public function getIndex()
    {
        $msg_remark = '审核内容仅限“后台审核”状态录';
        if (get_config('dividend_auto_send_regular', 0) == 1) {
            $msg_remark = '审核内容包括“上级审核”状态';
        }
        return view('dividend.report', [
            'msg_remark' => $msg_remark,
            'user_type' => UserType::all(),
            'user_group' => UserGroup::all(),
            'start_date' => date('Y-m-d 00:00:00', strtotime('-1 days')),
            'end_date' => date('Y-m-d 00:00:00', strtotime('+1 days')),
        ]);
    }

    public function postIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = [];
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');

            $param = [];
            $param['calculate_total'] = $request->get('calculate_total');

            $username = $request->get('username', '');
            $status = $request->get('status');
            $send_type = $request->get('send_type');
            $start_time = $request->get('start_time');
            $end_time = $request->get('end_time');
            $period = $request->get('period');
            $user_level = (int)$request->get('user_level', 0);
            $dividend_type = (int)$request->get('dividend_type', 0);

            $parent_username = $request->get('parent_username');
            $parent = null;
            if (!empty($parent_username)) {
                $parent = User::select(['id'])->where('username', $parent_username)->first();
            }

            $where = function ($query) use ($username, $status, $start_time, $end_time, $parent, $send_type, $period, $user_level, $dividend_type) {
                if (!empty($username)) {
                    $query->where('users.username', '=', $username);
                }

                if (!empty($status) && $status != 'all') {
                    $query->where('contract_dividends.status', '=', $status);
                }

                if (!empty($start_time)) {
                    $query->where('contract_dividends.start_time', '>=', $start_time);
                }

                if (!empty($end_time)) {
                    $query->where('contract_dividends.end_time', '<=', $end_time);
                }

                if (!empty($parent) && $parent->id > 0) {
                    $query->where(function ($query) use ($parent) {
                        $query->where('users.parent_tree', '@>', $parent->id)
                            ->orWhere('users.id', '=', $parent->id);
                    });
                }

                if (!empty($send_type) && $send_type != 'all') {
                    $query->where('contract_dividends.send_type', '=', $send_type);
                }

                if (!empty($period) && $period != 'all') {
                    $query->where('contract_dividends.period', '=', $period);
                }

                if ($user_level) {
                    $query->where(DB::raw('jsonb_array_length(users.parent_tree)'), '=', $user_level);
                }

                if ($dividend_type) {
                    $query->where('contract_dividends.type', '=', $dividend_type);
                }
            };

            // 计算过滤后总数
            $data['recordsTotal'] = $data['recordsFiltered'] = ContractDividend::leftJoin('users', 'users.id', 'contract_dividends.user_id')
                ->where($where)
                ->count();

            // 统计所有金额
            if ($param['calculate_total'] == '2' && $data['recordsTotal'] < 1000000) {
                $sum_amount = ContractDividend::select([
                    DB::RAW('COALESCE(SUM(contract_dividends.total_profit),0) as total_profit'),
                    DB::RAW('COALESCE(SUM(contract_dividends.amount),0) as amount')
                ])
                    ->leftJoin('users', 'users.id', 'contract_dividends.user_id')
                    ->where($where)
                    ->first();

                $data['sum_amount'] = ['total' => 0, 'real_amount' => 0];

                if ($sum_amount) {
                    $data['sum_amount'] = $sum_amount;
                }
            }

            $data['data'] = ContractDividend::select([
                'contract_dividends.*',
                DB::raw("CASE WHEN users.user_type_id!=1 THEN concat(jsonb_array_length(users.parent_tree),'级',user_type.name) ELSE user_type.name END as user_type_name"),
                'users.username'
            ])
                ->leftJoin('users', 'users.id', 'contract_dividends.user_id')
                ->leftJoin('user_type', 'user_type.id', 'users.user_type_id')
                ->where($where)
                ->skip($start)->take($length);

            if (!empty($parent->id)) {
                $data['data'] = $data['data']->orderByRaw(DB::raw("CASE contract_dividends.user_id WHEN {$parent->id} THEN 0 ELSE contract_dividends.id END asc"))->get();
            } else {
                $data['data'] = $data['data']->orderBy("contract_dividends.id", 'ASC')->get();
            }

            foreach ($data['data'] as $tmp_k => $row) {
                $data['data'][$tmp_k]['start_date'] = date('Y-m-d', strtotime($row['start_time']));
                $data['data'][$tmp_k]['end_date'] = date('Y-m-d', strtotime($row['end_time']));
            }

            return response()->json($data);
        }
    }

    /**
     * 契约审核
     * @param Request $request
     */
    public function putCheck(Request $request)
    {
        $id = $request->get('id');
        $status = (int)$request->get('status');

        $apiDividend = new Dividend();
        if (is_numeric($id)) {
            $result = $apiDividend->verify($id, $status, true);
        } else {
            preg_match_all("/(\d+)[\s|,]?/", $id, $ids);
            $ids = array_unique($ids[1]);
            $result = $apiDividend->batchVerify($ids, $status);
        }
        return response()->json(['status' => ($result) ? 0 : 1, 'msg' => $apiDividend->err_msg]);
    }

    public function postDetail(Request $request)
    {
        $id = (int)$request->post('id', 0);
        $fields = [
            'contract_dividends.*',
            'users.username',
            DB::raw("CASE WHEN users.user_type_id!=1 THEN concat(jsonb_array_length(users.parent_tree),'级',user_type.name) ELSE user_type.name END as user_type_name"),
            'user_group.name as user_group_name',
        ];
        $detail = ContractDividend::select($fields)
            ->leftJoin('users', 'users.id', 'contract_dividends.user_id')
            ->leftJoin('user_type', 'user_type.id', 'users.user_type_id')
            ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
            ->where('contract_dividends.id', $id)->first();

        $detail->extra = json_decode($detail->extra, true);

        if (isset($detail->extra['commission'])) {
            return view('dividend.commission', [
                'detail' => $detail,
                'commission' => $detail->extra['commission']
            ]);
        }

        return view('dividend.detail', [
            'detail' => $detail,
        ]);
    }

    /**
     * 删除无效分红报表
     * @param Request $request
     * @return $this
     */
    public function deleteIndex(Request $request)
    {
        $id = $request->get('id', '');
        if (empty($id)) {
            return response()->json(['status' => 1, 'msg' => '删除失败，请指定删除ID']);
        }
        preg_match_all("/(\d+)[\s|,]?/", $id, $ids);
        $ids = array_unique($ids[1]);
        $builder = ContractDividend::whereIn('id', $ids)
            ->where('status', 4);
        $count = $builder->count();
        if ($count > 0 && $count <= 100) {
            $af = $builder->delete();
            return response()->json(['status' => 0, 'msg' => '成功删除 ' . $af . ' 条纪录']);
        } else {
            return response()->json(['status' => 1, 'msg' => '没有符合条件的分红纪录（只允许删除后台审核状态的分红纪录）']);
        }
    }
}

<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\PointOrdersIndexRequest;
use Service\Models\Admin\AdminUser;
use Service\Models\PointOrders;
use Illuminate\Support\Facades\DB;

class PointordersController extends Controller
{
    public function getIndex(Request $request)
    {
        $data['order_type'] = $request->get('order_type', '');
        $data['order_no'] = $request->get('order_no', '');
        $data['admin_list'] = AdminUser::all(['id', 'username']);

        $data['start_date'] = Carbon::today();
        $data['end_date'] = Carbon::tomorrow();
        $data['amount_min'] = $request->get('amount_min', '');
        $data['amount_max'] = $request->get('amount_max', '');
        $data['username'] = $request->get('username', '');
        return view('point-orders.index', $data);
    }

    /**
     * 账变列表数据
     *
     * @param PointOrdersIndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postIndex(PointOrdersIndexRequest $request)
    {
        if ($request->ajax()) {
            $data = array();
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');

            $param = array();

            $param['order_type'] = $request->get('order_type');                //账变类型
            $param['order_no'] = id_decode($request->get('order_no', '')); //订单号
            $param['admin_user_id'] = (int)$request->get('admin_user_id');        //管理员ID
            $param['start_date'] = $request->get('start_date');              //开始时间
            $param['end_date'] = $request->get('end_date');                  //结束时间
            $param['amount_min'] = $request->get('amount_min', '');   //最小积分
            $param['amount_max'] = $request->get('amount_max', '');   //最大积分
            $param['username'] = (string)$request->get('username');          //用户名

            //查询条件
            $where = function ($query) use ($param) {
                if ($param['order_type'] != 'all') {
                    $query->where('point_orders.order_type', $param['order_type']);
                }

                if (!empty($param['order_no'])) {
                    $query->where('point_orders.id', $param['order_no']);
                }

                if (!empty($param['admin_user_id'])) {
                    $query->where('point_orders.admin_id', $param['admin_user_id']);
                }

                if (!empty($param['start_date'])) {
                    $query->where('point_orders.created_at', '>=', $param['start_date']);
                }

                if (!empty($param['end_date'])) {
                    $query->where('point_orders.created_at', '<', $param['end_date']);
                }

                if ($param['amount_min'] != '' && $param['amount_min'] >= 0) {
                    $query->where('point_orders.amount', ">=", $param['amount_min']);
                }

                if ($param['amount_max'] != '' && $param['amount_max'] >= 0) {
                    $query->where('point_orders.amount', "<=", $param['amount_max']);
                }

                if (!empty($param['username'])) {
                    $query->where('users.username', $param['username']);
                }
            };

            // 计算过滤后总数
            $count_query = PointOrders::leftJoin('users', 'users.id', 'point_orders.user_id')->where($where);
            $count_sql = vsprintf(str_replace(array('?'), array('\'\'%s\'\''), $count_query->select([DB::raw(1)])->toSql()), $count_query->getBindings());
            $count = DB::selectOne("
                select count_estimate('{$count_sql}') as total
            ");

            if ($count->total > 20000) {
                $data['recordsTotal'] = $data['recordsFiltered'] = $count->total;
            } else {
                $data['recordsTotal'] = $data['recordsFiltered'] = $count_query->count();
            }

            $data['data'] = PointOrders::select([
                'point_orders.id as order_id',
                'point_orders.created_at',
                'users.username',
                'point_orders.order_type as order_type_id',
                'point_orders.order_type',
                DB::raw('case when point_orders.order_type = 0 then \'+\' || cast(point_orders.amount as varchar) else \'-\' end as amount_add'),
                DB::raw('case when point_orders.order_type = 1 then \'-\' || cast(point_orders.amount as varchar) else \'-\' end as amount_sub'),
                'point_orders.points',
                'point_orders.relate_type',
                'point_orders.description',
                DB::raw('COALESCE(admin_users.username, \'-\') as adminname'),
                'point_orders.relate_id',
            ])
                ->leftJoin('users', 'users.id', 'point_orders.user_id')
                ->leftJoin('admin_users', 'admin_users.id', 'point_orders.admin_id')
                ->where($where)
                ->skip($start)->take($length)
                ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                ->get();

            if ($data['data']) {
                $param_list = $this->_paramList();
                $order_type_list = $param_list['order_type_list'];
                $relate_type_list = $param_list['relate_type_list'];

                foreach ($data['data'] as $k => $v) {
                    $data['data'][$k]->order_id = id_encode($v->order_id);
                    $data['data'][$k]->order_type = isset($order_type_list[$v->order_type]) ? $order_type_list[$v->order_type] : '-';
                    $data['data'][$k]->relate_type = isset($relate_type_list[$v->relate_type]) ? $relate_type_list[$v->relate_type] : '-';
                }
            }

            return response()->json($data);
        }
    }

    /**
     * 查询搜索框和数据列表显示的参数列表
     *
     * @return array
     */
    private function _paramList()
    {
        return [
            'order_type_list' => [0 => "增加", 1 => "扣除"],
            'relate_type_list' => [0 => "投注", 1 => "积分兑换", 2 => "管理员操作"],
        ];
    }
}

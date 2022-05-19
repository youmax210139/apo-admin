<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProjectsFeeReportIndexRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Service\Models\User;
use Service\Models\UserType;
use Service\Models\UserGroup;
use Service\API\User as APIUser;

class ProjectsfeereportController extends Controller
{
    public function getIndex(Request $request)
    {
        $default_search_time = get_config('default_search_time', 0);
        return view('projects-fee-report.index', [
            'user_type' => UserType::all(),
            'user_group' => UserGroup::all(),
            'id' => (int)$request->get('id', 0),
            'start_date' => Carbon::now()->hour >= $default_search_time ?
                Carbon::today()->addHours($default_search_time) :
                Carbon::yesterday()->addHours($default_search_time),
            'end_date' => Carbon::now()->hour >= $default_search_time ?
                Carbon::tomorrow()->addHours($default_search_time)->subSecond(1) :
                Carbon::today()->addHours($default_search_time)->subSecond(1),
        ]);
    }

    public function postIndex(ProjectsFeeReportIndexRequest $request)
    {
        if ($request->ajax()) {
            $param['id'] = (int)$request->post('id', 0);
            $param['username'] = (string)$request->post('username');
            $param['start_date'] = $request->post('start_date', Carbon::today());
            $param['end_date'] = $request->post('end_date', Carbon::today()->endOfDay());
            $param['user_group_id'] = (int)$request->post('user_group_id');
            $param['is_search'] = (int)$request->post('is_search');

            if ($param['id'] > 0 && empty($param['is_search'])) {
                $api_user = new APIUser();
                $data['parent_tree'] = $api_user->getParentTreeByParentID($param['id'], false);
            }

            if ($param['is_search'] && !empty($param['username'])) {
                $api_user = new APIUser();
                $data['parent_tree'] = $api_user->getParentTreeByUserName($param['username'], false);

                $param['id'] = 0;
                $user = User::where('username', $param['username'])->first(['id']);

                if (!empty($user)) {
                    $param['id'] = $user->id;
                }
            }

            $order = 'total_fee desc';

            $join_type = 'left';
            if ($param['id']) {
                $user = User::find($param['id']);

                $level = 0;
                if (!empty($user)) {
                    $level = count(json_decode($user->parent_tree, true)) + 1;
                }

                $where_pf = '';

                $where_bind_pf = [
                    'user_id_pf' => $param['id'],
                    'id_pf' => $param['id']
                ];

                if (!empty($param['start_date'])) {
                    $where_pf .= ' and pf.created_at >= :start_date_pf';
                    $where_bind_pf['start_date_pf'] = $param['start_date'];

                }

                if (!empty($param['end_date'])) {
                    $where_pf .= ' and pf.created_at <= :end_date_pf';
                    $where_bind_pf['end_date_pf'] = $param['end_date'];
                }

                $_data = DB::select("
                    WITH report_projects_fee AS (
                        select
                        pf.user_id,
                        SUM(pf.amount) as total_fee
                        from projects_fee as pf
                        left join users on users.id = pf.user_id
                        where (users.parent_tree @> :user_id_pf or users.id = :id_pf)
                        and pf.status = 0
                        {$where_pf}
                        group by pf.user_id
                    ), report_projects_fee_total AS (
                        select
                            COALESCE (CAST (users.parent_tree->>{$level} AS INTEGER), users.id) AS user_id,
                            SUM(report_projects_fee.total_fee) as total_fee
                        from report_projects_fee
                        left join users ON user_id = users.id
                        group by 1
                    )
                    select
                      users.id as user_id,
                      users.username,
                      user_group.id as user_group_id,
                      user_group.name as user_group_name,
                      COALESCE(report.total_fee, 0) as total_fee
                      from (
                        select
                            user_id,
                            total_fee
                        from report_projects_fee_total
                    ) as report
                    {$join_type} join users on users.id = report.user_id
                    left join user_group on users.user_group_id = user_group.id
                    order by {$order}
                ", array_merge(
                    $where_bind_pf
                ));

                $self = null;
                foreach ($_data as $key => $__data) {
                    if ($__data->user_id == $param['id']) {
                        $self = $__data;
                        unset($_data[$key]);
                        break;
                    }
                }

                if ($self) {
                    $self->self = 1;
                    array_unshift($_data, $self);
                }
                $data['data'] = $_data;
            } else {
                $where_pf = '';
                $where_bind_pf = [];
                if (!empty($param['start_date'])) {
                    $where_pf .= ' and pf.created_at >= :start_date_pf';
                    $where_bind_pf['start_date_pf'] = $param['start_date'];
                }

                if (!empty($param['end_date'])) {
                    $where_pf .= ' and pf.created_at <= :end_date_pf';
                    $where_bind_pf['end_date_pf'] = $param['end_date'];
                }

                if (!empty($param['user_group_id'])) {
                    $where_pf .= ' and users.user_group_id = :user_group_id_pf';
                    $where_bind_pf['user_group_id_pf'] = $param['user_group_id'];
                }

                $data['data'] = DB::select("
                    select
                      users.id as user_id,
                      users.username,
                      user_group.id as user_group_id,
                      user_group.name as user_group_name,
                      COALESCE(report.total_fee, 0) as total_fee
                    from (
                      select
                        user_id,
                        total_fee
                      from (
                          select
                            users.top_id as user_id,
                            SUM(pf.amount) as total_fee
                          from projects_fee as pf
                          left join users on users.id = pf.user_id
                          where pf.status = 0
                          {$where_pf}
                          group by users.top_id
                      ) as projects_fee_report

                    ) as report
                    {$join_type} join users on users.id = report.user_id
                    left join user_group on users.user_group_id = user_group.id
                    order by {$order}
                ",
                    array_merge(
                        $where_bind_pf
                    ));
            }

            return $data;
        }
    }

}

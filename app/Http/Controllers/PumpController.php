<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2021/2/4
 * Time: 14:30
 */

namespace App\Http\Controllers;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Service\Models\UserGroup as ModelUserGroup;
use Service\Models\User as ModelUser;
use Service\Models\Projects as ModelProjects;
use Service\Models\PumpOutlet as ModelPumpOutlet;
use Service\API\User as APIUser;

/**
 * 抽水纪录查询
 * Class PumpinletController
 * @package App\Http\Controllers
 */
class PumpController extends Controller
{

    public function getIndex(Request $request)
    {
        $data_types = $this->getDataTypes();
        $param = $this->getParam($request);
        $user_group = ModelUserGroup::all();
        $view = 'index';
        //获取彩种信息
        $lottery_list = \Service\API\Lottery::getAllLotteryGroupByCategory();
        return view('pumps.index',[
            'view' => $view,
            'data_types' => $data_types,
            'lottery_list' => $lottery_list,
            'user_group' => $user_group,
            'param' => $param,
        ]);
    }

    public function postIndex(Request $request)
    {
        $start = $request->get('start');
        $length = $request->get('length');
        $order = $request->get('order');
        $columns = $request->get('columns');
        $param = $this->getParam($request);
        if($param['username'] && !$param['user_id'] ){
            return [
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => 0,
                'sum' => 0,
            ];
        }
        //抽水数据还是返水数据
        $table_name = 'pump_inlets';
        if($param['data_type'] == 'outlet'){
            $table_name = 'pump_outlets';
        }

        //查询条件
        $where = function (Builder $query) use ($param,$table_name) {
            if (!empty($param['user_id'])) {
                if($param['include_all']){
                    $query->where(function ($query) use ($param) {
                        $query->where('users.id','=', $param['user_id'])
                            ->orWhere('users.parent_tree', '@>', $param['user_id']);
                    });
                }else{
                    $query->where('users.id', $param['user_id']);
                }
            }
            if (!empty($param['user_group_id'])) {
                $query->where('users.user_group_id', '=', $param['user_group_id']);
            }
            if (!empty($param['project_id'])) {
                $query->where($table_name.'.project_id', '=', $param['project_id']);
            }
            if (!empty($param['lottery_id'])) {
                $query->where('projects.lottery_id', '=', $param['lottery_id']);
            }
            if (!empty($param['issue'])) {
                $query->where('projects.issue', '=', $param['issue']);
            }

            if (!empty($param['start_time'])) {
                $query->where($table_name.'.created_at', '>=', $param['start_time']);
            }
            if (!empty($param['end_time'])) {
                $query->where($table_name.'.created_at', '<=', $param['end_time']);
            }
            if (!empty($param['amount_min'])) {
                $query->where($table_name.'.amount', '>=', $param['amount_min']);
            }
            if (!empty($param['amount_max'])) {
                $query->where($table_name.'.amount', '<=', $param['amount_max']);
            }
            if (!empty($param['status'])) {
                $query->where($table_name.'.status', '=', $param['status']);
            }

        };
        $query = DB::table($table_name)
            ->leftJoin('users','users.id',$table_name.'.user_id')
            ->leftJoin('projects','projects.id',$table_name.'.project_id')
            ->where($where);
        $data['param'] = $param;
        $data['recordsTotal'] = $data['recordsFiltered'] = $query->count();
        $data['sum'] = $query->sum($table_name.'.amount');
        $data['data'] = $query->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
            ->leftJoin('user_type', 'user_type.id', 'users.user_type_id')
            ->leftJoin('lottery', 'lottery.id', 'projects.lottery_id')
            ->skip($start)->take($length)
            ->orderBy($table_name.'.'.$columns[$order[0]['column']]['data'], $order[0]['dir'])
            ->get([
                $table_name.'.*','users.username','users.user_type_id','user_group.name as user_group_name',
                'lottery.name as lottery_name','projects.issue',
                DB::raw("CASE WHEN users.user_type_id = 2 THEN concat(jsonb_array_length(users.parent_tree),'级',user_type.name) ELSE user_type.name END as user_type_name"),
            ]);
        foreach ($data['data'] as $k => $v) {
            $data['data'][$k]->project_id = id_encode($v->project_id);
        }
        return $data;
    }


    public function getDetail(Request $request)
    {
        $id = id_decode($request->get('id', ''));
        if(!$id){
            return redirect('/pump\/')->withErrors("无效的ID");
        }
        $project = ModelProjects::select(
            [
                'projects.*',
                'projects.id as project_no',
                'users.id as uid',
                'users.username',
                'users.user_type_id','user_group.name as user_group_name',
                DB::raw("CASE WHEN users.user_type_id = 2 THEN concat(jsonb_array_length(users.parent_tree),'级',user_type.name) ELSE user_type.name END as user_type_name"),
                'i.code as bonus_code',
                'i.sale_end',
                'i.sale_start',
                'package.code as mmc_code',
                'package.process_time',
                'l.name as lottery_name',
                'l.lottery_category_id',
                'lm.prize_level_name',
                DB::raw('(plm.name || \' - \' || lm.name) as method_name'),
                'pi.id as  pump_inlet_id',
                'pi.cardinal as pump_inlet_cardinal',
                'pi.scale as  pump_inlet_scale',
                'pi.amount as  pump_inlet_amount',
                'pi.outlet_amount as  pump_outlet_amount',
                'pi.status as  pump_inlet_status',
                'pi.extend as  pump_inlet_extend',
                'pi.created_at as  pump_inlet_created_at',
                'pi.updated_at as  pump_inlet_updated_at',
            ]
        )
            ->leftJoin('users', 'users.id', 'projects.user_id')
            ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
            ->leftJoin('user_type', 'user_type.id', 'users.user_type_id')
            ->leftJoin('package', 'package.id', 'projects.package_id')
            ->leftJoin('lottery as l', 'l.id', 'projects.lottery_id')
            ->leftJoin('lottery_method as lm', 'lm.id', 'projects.lottery_method_id')
            ->leftJoin('lottery_method as plm', 'lm.parent_id', 'plm.id')
            ->leftJoin('issue as i', function ($join) {
                $join->on('i.lottery_id', 'projects.lottery_id')->on('i.issue', 'projects.issue');
            })
            ->leftJoin('pump_inlets as pi', 'pi.project_id', 'projects.id')
            ->where('projects.id', $id)->first();
        if(!$project){
            return redirect('/pump\/')->withErrors("查找不到对应ID的纪录".$id);
        }
        $project->project_no = id_encode($project->project_no);
        $pump_outlets = ModelPumpOutlet::leftJoin('users', 'users.id', 'pump_outlets.user_id')
            ->where('project_id',$project->id)
            ->select(['pump_outlets.*','users.username as username'])
            ->get();
        $api_user = new APIUser();
        $parent_tree = $api_user->getParentTreeByParentID($project->user_id, false);
        return view('pumps.detail',[
            'detail' => $project,
            'parent_tree' => $parent_tree,
            'pump_outlets' => $pump_outlets,
        ]);

    }

    protected function getParam(Request $request)
    {
        $data_types = $this->getDataTypes();
        $data_type = $request->get('data_type', 'inlet');//查询的数据
        $param['data_type'] = isset($data_types[$data_type])?$data_type:'inlet';
        $param['username'] = $request->get('username', '');//用户名
        $param['project_no'] = $request->get('project_no', '');//注单编号
        $param['user_group_id'] = $request->get('user_group_id', 0);//组别
        $param['start_time'] = $request->get('start_time', Carbon::today());//开始时间
        $param['end_time'] = $request->get('end_time', Carbon::tomorrow()->subSecond());//结束时间
        $param['amount_min'] = $request->get('amount_min', 0);
        $param['amount_max'] = $request->get('amount_max', 0);
        $param['lottery_id'] = $request->get('lottery_id', 0);//彩种
        $param['issue'] = $request->get('issue', 0);//奖期
        $param['status'] = $request->get('status', 0);//抽返水状态
        $param['include_all'] = $request->get('include_all', 0);//是否显示全部下级
        $param = array_map('trim',$param);
        if ($param['project_no']){
            $param['project_id'] = id_decode($param['project_no']);
        }else{
            $param['project_id'] = 0;
        }
        if($param['username']){
            $param['user_id'] = ModelUser::where('username',$param['username'])->value('id');
        }
        return $param;
    }

    /**
     * 获取数据类型
     * @return array
     */
    protected function getDataTypes()
    {
        return [
            'inlet' => '抽水',
            'outlet' => '返水',
        ];
    }

    protected function getSearchTypes()
    {
        return ['个人','团队'];
    }
}
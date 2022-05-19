<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2021/2/4
 * Time: 14:34
 */

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Service\Models\PumpRule;
use Service\Models\User as ModelUser;
use Service\Models\UserGroup as ModelUserGroup;
use Service\Models\PumpRule as ModelPumpRule;
use Service\API\User as APIUser;
use Service\API\Pump\Rule as ApiPumpRule;
/**
 * 抽返水规则
 * Class PumpruleController
 * @package App\Http\Controllers
 */
class PumpruleController
{
    public function getIndex(Request $request)
    {
        $param = $this->getSearchParam($request);
        $user_group = ModelUserGroup::all();
        return view('pump-rules.index',[
            'user_group' => $user_group,
            'param' => $param,
        ]);

    }

    public function postIndex(Request $request)
    {
        $pump_default_rule = json_decode(get_config('pump_default_rule',''),true);
        $param = $this->getSearchParam($request);
        //查询条件
        $where = function ($query) use ($param) {
            if($param['user_id']){
                if($param['include_all']){
                    $query->where(function ($query) use ($param) {
                        $query->where('users.id', $param['user_id'])
                            ->orWhere('users.parent_tree', '@>', $param['user_id']);
                    });
                }else{
                    $query->where(function ($query) use ($param) {
                        $query->where('users.id', $param['user_id'])
                        ->orWhere('users.parent_id', '=', $param['user_id']);
                    });
                }
            }else{
                $query->where('users.parent_id', 0);
            }
            if($param['user_group_id']){
                $query->where('users.user_group_id', $param['user_group_id']);
            }

        };
        $api_user = new APIUser();
        if($param['user_id']){
            $data['parent_tree'] = $api_user->getParentTreeByParentID($param['user_id'], false);
        }
        $query = ModelUser::leftJoin('user_group','user_group.id','users.user_group_id')
            ->where($where);
        if($param['search_type'] == 1){
            $query->leftJoin('pump_rules','pump_rules.user_id', 'users.id')->where('pump_rules.status','=','0');
        }else{
            $query->leftJoin('pump_rules', function ($join) {
                $join->on('pump_rules.user_id', '=', 'users.id')
                    ->where('pump_rules.status','=','0');
            });
        }

        $data['recordsTotal'] = $data['recordsFiltered'] = $query->count();
        $users = $query->leftJoin('user_type', 'user_type.id', 'users.user_type_id')->get(['users.id as user_id',
            'users.top_id as top_id',
            'users.parent_id as parent_id',
            'users.username as username',
            'users.parent_tree as parent_tree',
            'users.user_type_id',
            'users.user_group_id',
            'user_type.name as user_type_name',
            'user_group.name as user_group_name',
            'pump_rules.id as rule_id',
            'pump_rules.content as rule_content',
            'pump_rules.status as rule_status',
            'pump_rules.created_at as rule_created_at',
        ]);
        if($users->isNotEmpty()){
            foreach ($users as &$user){
                if($user->user_type_id == 2 && !empty($user->parent_tree)){
                    if(!is_array($user->parent_tree)){
                        $user->parent_tree  = json_decode($user->parent_tree,true);
                    }
                    $user->user_type_name = count($user->parent_tree).'级'.$user->user_type_name;
                }
                $user->rule_id = empty($user->rule_id)?0:$user->rule_id;
                $user->rule_enable = 0;
                if(!empty($user->rule_content)){
                    $user->rule_content = json_decode($user->rule_content,true);
                    $user->rule_enable = isset($user->rule_content['enable'])??$user->rule_content['enable'];
                    if (isset($user->rule_content['inlet'])){
                        $scale_arr = array_column($user->rule_content['inlet'],'scale');
                        $user->rule_scale_max = max($scale_arr);
                    }
                }else{
                    if($user->user_id == $user->top_id){
                        //如果没设置且为总代，则读取默认配置
                        $scale_arr = array_column($pump_default_rule['inlet'],'scale');
                        $user->rule_scale_max = max($scale_arr);
                    }
                }
            }
        }
        $data['data'] = $users;
        return $data;
    }

    public function getDetail(Request $request)
    {
        $user_id = intval($request->get('user_id'));
        $user = $this->_detail($user_id);
    }

    public function getHistory(Request $request)
    {
        $user_id = intval($request->get('user_id'));
        $have_enable = 0;
        if(!$self = $this->_singleDetail($user_id)){
            return redirect('/pumprule/')->withErrors('无效的用户ID');
        }
        $api_pump_rule = new ApiPumpRule();
        $pump_default_rule = get_config('pump_default_rule','');
        $pump_default_rule_columns = [];
        if($pump_default_rule){
            $pump_default_rule = json_decode($pump_default_rule,true);
            $pump_default_rule['inlet'] = multi_array_sort($pump_default_rule['inlet'], 'scale', SORT_DESC);
            $pump_default_rule_columns = $pump_default_rule?$api_pump_rule->getInletRuleIncludes($pump_default_rule['conditions']):[];
        }
        $rules = PumpRule::where('user_id',$user_id)->orderBy('status','asc')->orderBy('id','desc')->get();
        if($rules->isNotEmpty()){
            foreach ($rules as &$rule){
                if($rule->status == 0){
                    $have_enable = 1;
                }
                $rule->conten_inlet = multi_array_sort($rule->content['inlet'], 'scale', SORT_DESC);
                $rule->content_columns = $api_pump_rule->getInletRuleIncludes($rule->content['conditions']);
            }
        }
        $api_user = new APIUser();
        $parent_tree = $api_user->getParentTreeByParentID($user_id, false);
        return view('pump-rules.history',[
            'self' => $self,
            'rules' => $rules,
            'pump_default_rule' => $pump_default_rule,
            'pump_default_rule_columns' => $pump_default_rule_columns,
            'have_enable' => $have_enable,
            'parent_tree' => $parent_tree,
        ]);
    }

    public function deleteIndex(Request $request)
    {

        $user_id = (int)$request->get('user_id', 0);
        $user = ModelUser::find($user_id);
        if (!$user) {
            return response()->json(['status' => -1, 'msg' => '找不到该用户!']);
        }
        $af = PumpRule::join('users', 'users.id', 'pump_rules.user_id')
            ->where(function ($query) use ($user_id) {
                $query->where('users.parent_tree', '@>', $user_id)
                    ->orWhere('user_id', $user_id);
            })
            ->where('status', 0)
            ->update([
                'status' => 1,
                'stage' => 2,
                'updated_username' => auth()->user()->username,
            ]);
        return response()->json(['status' => 0, 'msg' => '成功删除 '.$af.' 条 '.$user->username.' 及其下级的抽返水规则!']);

    }

    public function getCreate(Request $request)
    {
        $user_id = intval($request->get('user_id'));
        $user = $this->_singleDetail($user_id);
        if (!$user) {
            return response()->json(['status' => -1, 'msg' => '找不到该用户!']);
        }

        if(!$third_users = $this->_detail($user_id)){
            return redirect('/pumprule/')->withErrors('无效的用户ID');
        }
        list($top,$parent,$self) = $third_users;
        $api_pump_rule = new ApiPumpRule();
        $rule_conditions = $api_pump_rule->getInletRuleIncludes($top->rule_content['conditions']);
        return view('pump-rules.create_inlet',[
            'user' => $user,
            'self' => $self,
            'parent' => $parent,
            'top' => $top,
            'conditions' => $rule_conditions,
        ]);
    }

    public function postCreate(Request $request)
    {
        $step = $request->post('step',0);
        $self_id = intval($request->post('user_id'));
        if(!$third_users = $this->_detail($self_id)){
            return redirect('/pumprule/')->withErrors('无效的用户ID');
        }
        list($top,$parent,$self) = $third_users;
        switch ($step){
            case 1:
                $inlets = [];
                //dd($request->post());
                $post_data = $request->post();
                $api_pump_rule = new ApiPumpRule();
                $rule_conditions = $api_pump_rule->getInletRuleIncludes($top->rule_content['conditions']);
                for ($i = 0;$i<count($post_data['scale']);$i++){
                    foreach ($rule_conditions as $_c_k => $condition){
                        $inlets[$i][$_c_k] = $post_data[$_c_k][$i];
                    }
                }
                dump($inlets);
                $inlets = $api_pump_rule->checkInletContent($inlets,$top->rule_content['conditions']);
                if(!$inlets){
                    return redirect('/pumprule/create?user_id='.$self_id)->withErrors($api_pump_rule->messages);
                }
                dump($api_pump_rule->messages);
                dd($inlets);

                $bonus_arr = $request->post('bonus');
                $bet_arr = $request->post('bet');
                $profit_arr = $request->post('profit');
                $scale_arr = $request->post('scale');
                $inlets = [];
                $api_pump_rule = new ApiPumpRule();
                $rule_conditions = $api_pump_rule->getInletRuleIncludes($top->rule_content['conditions']);


                if(!$third_users = $this->_detail($self_id)){
                    return redirect('/pumprule/')->withErrors('无效的用户ID');
                }
                list($top,$parent,$self) = $third_users;
                $api_pump_rule = new ApiPumpRule();
                $rule_conditions = $api_pump_rule->getInletRuleIncludes($top->rule_content['conditions']);
                return view('pump-rules.create_inlet',[
                    'self' => $self,
                    'parent' => $parent,
                    'top' => $top,
                    'conditions' => $rule_conditions,
                ]);
                break;
            case 2:
                break;
            default:

                break;
        }

    }

    public function getEdit(Request $request)
    {
        return $this->getCreate($request);
    }

    public function putEdit(Request $request)
    {
        return $this->postCreate($request);
    }

    public function _detail($self_id)
    {
        $self = $this->_singleDetail($self_id,true);
        if(!$self){
            return false;
        }
        if(!empty($self->rule_content) && !is_array($self->rule_content)){
            $self->rule_content = json_decode($self->rule_content,true);
        }
        //判断是否为总代
        if($self->top_id == $self->user_id){
            //如果是总代则三个是一样的
            $top = $parent = $self;
        }else{
            //如果本身不是总代的话，要获取总代抽水规则
            $top = $this->_singleDetail($self->top_id,true);
            if($self->parent_id == $self->top_id) {
                //如果上级就是总代
                $parent = $top;
            }else{
                //之所以要取上级是因为设置的时候需要一个参照物
                $parent = $this->_singleDetail($self->parent_id ,true);
                if(!empty($parent->rule_content) && !is_array($parent->rule_content)){
                    $parent->rule_content = json_decode($parent->rule_content,true);
                }
            }
        }
        if(empty($top->rule_content)){
            $top->rule_content = get_config('pump_default_rule',ApiPumpRule::getDefaultContent());
        }
        if(!is_array($top->rule_content)){
            $top->rule_content = json_decode($top->rule_content,true);
        }
        return [$top,$parent,$self];
    }


    public function _singleDetail($user_id,$with_rule = false)
    {
        $builder = ModelUser::leftJoin('user_group','user_group.id','users.user_group_id')
            ->leftJoin('user_type', 'user_type.id', 'users.user_type_id')
            ->where('users.id',$user_id)
            ->select(['users.id as user_id',
                'users.top_id',
                'users.parent_id',
                'users.parent_tree',
                'users.username as username',
                'users.user_type_id',
                'users.user_group_id',
                'user_type.name as user_type_name',
                'user_group.name as user_group_name',

            ]);
        if ($with_rule){
            $builder->leftJoin('pump_rules', function ($join) {
                $join->on('pump_rules.user_id', '=', 'users.id')
                    ->where('pump_rules.status','=','0');
            })->select([
                'pump_rules.id as rule_id',
                'pump_rules.content as rule_content',
                'pump_rules.status as rule_status',
                'pump_rules.created_at as rule_created_at',
            ]);
        }
        $user = $builder->first();
        return $user;
    }



    public function getSearchParam(Request $request)
    {
        $param = [];
        $param['username'] = $request->get('username', '');//用户名
        if($param['username']){
            $param['user_id'] = ModelUser::where('username',$param['username'])->value('id');
        }else{
            $param['user_id'] = 0;
        }
        $param['user_group_id'] = $request->get('user_group_id', 0);//组别
        $param['search_type'] = $request->get('search_type', 0);//结果过滤 0 全部显示 1无规则不显示
        $param['include_all'] = $request->get('include_all', 0);//是否显示全部下级
        $param = array_map('trim',$param);
        return $param;
    }
}
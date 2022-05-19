<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Service\Models\User;

class UserqueryController extends Controller
{
    public function getIndex()
    {
        return view('user.userquery');
    }

    public function postIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = array();
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');

            $param = array();
            $param['account'] = (string)$request->get('account', '');              // 银行账号
            $param['account_name'] = (string)$request->get('account_name', '');    // 银行开户名
            $param['qq'] = (string)$request->get('qq', '');                        // QQ
            $param['telephone'] = (string)$request->get('telephone', '');          // 联系电话
            $param['weixin'] = (string)$request->get('weixin', '');                // 微信

            if (empty(array_filter($param))) {
                return response()->json(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
            }

            //查询条件
            $where = function ($query) use ($param) {
                if (!empty($param['account'])) {
                    $query->where("user_banks.account", $param['account']);
                }
                if (!empty($param['account_name'])) {
                    $query->where("user_banks.account_name", $param['account_name']);
                }
                if (!empty($param['qq'])) {
                    $query->where("user_profile1.value", $param['qq']);
                }
                if (!empty($param['telephone'])) {
                    $query->where("user_profile2.value", $param['telephone']);
                }
                if (!empty($param['weixin'])) {
                    $query->where("user_profile3.value", $param['weixin']);
                }
            };

            // 计算过滤后总数
            $data['recordsTotal'] = $data['recordsFiltered'] = User
                ::leftJoin('user_banks', 'user_banks.user_id', 'users.id')
                ->leftJoin('user_profile as user_profile1', function ($join) {
                    $join->on('user_profile1.user_id', '=', 'users.id')
                        ->where('user_profile1.attribute', '=', 'qq');
                })
                ->leftJoin('user_profile as user_profile2', function ($join) {
                    $join->on('user_profile2.user_id', '=', 'users.id')
                        ->where('user_profile2.attribute', '=', 'telephone');
                })
                ->leftJoin('user_profile as user_profile3', function ($join) {
                    $join->on('user_profile3.user_id', '=', 'users.id')
                        ->where('user_profile3.attribute', '=', 'weixin');
                })
                ->where($where)->withTrashed()->count();

            $data['data'] = User::select([
                'users.username',                       //用户名
                'top_users.username as top_username',   //所属总代
                'banks.name as bank_name',              //银行名
                'user_banks.account as bank_account',   //卡号
                'regions.name as province',             //省份
                'reg.name as city',                     //城市
                'users.frozen',                         //冻结类型id
                'users.user_group_id',                  //冻结类型id
                'user_profile1.value as qq',            //QQ
                'user_profile2.value as telephone',     //电话
                'user_profile3.value as weixin',        //微信
                'user_profile4.value as user_observe',  //重点观察原因
                'users.updated_at',                     //最后修改时间
                'users.created_at',                     //创建时间
            ])
                ->leftJoin('user_banks', 'user_banks.user_id', 'users.id')
                ->leftJoin('banks', 'banks.id', 'user_banks.bank_id')
                ->leftJoin('users as top_users', 'top_users.id', 'users.top_id')
                ->leftJoin('regions', 'regions.id', 'user_banks.province_id')
                ->leftJoin('regions as reg', 'reg.id', 'user_banks.city_id')
                ->leftJoin('user_profile as user_profile1', function ($join) {
                    $join->on('user_profile1.user_id', '=', 'users.id')
                        ->where('user_profile1.attribute', '=', 'qq');
                })
                ->leftJoin('user_profile as user_profile2', function ($join) {
                    $join->on('user_profile2.user_id', '=', 'users.id')
                        ->where('user_profile2.attribute', '=', 'telephone');
                })
                ->leftJoin('user_profile as user_profile3', function ($join) {
                    $join->on('user_profile3.user_id', '=', 'users.id')
                        ->where('user_profile3.attribute', '=', 'weixin');
                })
                ->leftJoin('user_profile as user_profile4', function ($join) {
                    $join->on('user_profile4.user_id', '=', 'users.id')
                        ->where('user_profile4.attribute', 'user_observe');
                })
                ->where($where)
                ->withTrashed()
                ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                ->skip($start)->take($length)
                ->get();

            //隐藏部分用户讯息
            $admin_user_id = auth()->id();
            $weixin_adminids = array_filter(explode(',', get_config('visible_weixin_adminids', '')), 'trim');
            $qq_adminids = array_filter(explode(',', get_config('visible_qq_adminids', '')), 'trim');
            $telephone_adminids = array_filter(explode(',', get_config('visible_telephone_adminids', '')), 'trim');

            foreach ($data['data'] as $record) {
                if ($record->weixin && !in_array($admin_user_id, $weixin_adminids)) {
                    $record->weixin = hide_str($record->weixin, 3, 4);
                }
                if ($record->qq && !in_array($admin_user_id, $qq_adminids)) {
                    $record->qq = hide_str($record->qq, 3, 4);
                }
                if ($record->telephone && !in_array($admin_user_id, $telephone_adminids)) {
                    $record->telephone = hide_str($record->telephone, 3, 4);
                }
            }

            return response()->json($data);
        }
    }
}

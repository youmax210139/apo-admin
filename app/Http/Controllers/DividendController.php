<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Service\API\Dividend\Dividend;
use Service\Models\User;
use Illuminate\Http\Request;
use Service\Models\UserGroup;
use Service\Models\UserType;
use Service\Models\UserDividendContract;
use Illuminate\Support\Facades\DB;

class DividendController extends Controller
{
    public function getIndex(Request $request)
    {
        return view('dividend.index', [
            'user_type' => UserType::all(),
            'user_group' => UserGroup::all(),
            'user_id' => $request->get('user_id') ?? '',
            'username' => $request->get('username') ?? '',
        ]);
    }

    public function postIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = [];
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');

            $username = $request->get('username', '');                  // 用户名
            $user_group_id = $request->get('user_group_id', 'all');     // 用户组ID
            $user_type_id = $request->get('user_type_id', 'all');       // 用户类型ID
            $include_all = $request->get('include_all');                       // 包含下级
            $status = $request->get('status', 0);                       // 状态

            $user_id = -1;
            if (!empty($username) && $include_all) {
                $user = User::select('id')->where('users.username', '=', $username)->first();
                if (!empty($user)) {
                    $user_id = $user->id;
                }
            }

            $where = function ($query) use ($username, $user_group_id, $user_type_id, $include_all, $user_id, $status) {
                if (!empty($username)) {
                    if ($include_all && $user_id > 0) {
                        $query->where(function ($query) use ($user_id) {
                            $query->where('users.id', '=', $user_id)->orWhere('users.parent_tree', '@>', $user_id);
                        });
                    } else {
                        $query->where('users.username', '=', $username);
                    }
                } else {
                    $query->where('users.parent_id', '=', '0');
                }

                if (!empty($user_group_id) && $user_group_id != 'all') {
                    $query->where('user_group.id', '=', $user_group_id);
                }

                if (!empty($user_type_id) && $user_type_id != 'all') {
                    $query->where('user_type.id', '=', $user_type_id);
                }

                if ($status > -1) {
                    $query->where(function ($query) {
                        // 获取平台派发最低用户层级
                        $dividend_send_low_level = get_config('dividend_send_low_level');
                        $query->where('user_dividend_contract.status', '=', 1)
                            //    ->orWhereRaw("jsonb_array_length(users.parent_tree) <= ".$dividend_send_low_level);
                            ->orWhereRaw("jsonb_array_length(users.parent_tree) <= ?", $dividend_send_low_level);
                    });
                }
            };

            //计算过滤后总数
            $data['recordsTotal'] = $data['recordsFiltered'] = User::leftJoin('user_dividend_contract', 'users.id', 'user_dividend_contract.user_id')
                ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
                ->leftJoin('user_type', 'user_type.id', 'users.user_type_id')
                ->where($where)
                ->count();

            $data['data'] = User::select([
                "users.id as id",
                "users.username as username",
                "user_profile1.value as user_observe",
                "user_group.id as user_group_id",
                "user_group.name as user_group_name",
                "users.user_type_id as user_type_id",
                DB::raw("CASE WHEN users.user_type_id!=1 THEN concat(jsonb_array_length(users.parent_tree),'级',user_type.name) ELSE user_type.name END as user_type_name"),
                DB::raw("jsonb_array_length(users.parent_tree) as user_level"),
                "user_dividend_contract.created_at",
                "user_dividend_contract.delete_at",
                "user_dividend_contract.content",
                "user_dividend_contract.mode",
                "top_uc.type as top_type",
                "user_dividend_contract.status"
            ])
                ->leftJoin('user_dividend_contract', function ($join) {
                    $join->on('user_dividend_contract.user_id', 'users.id')->where('user_dividend_contract.status', '1');
                })
                ->leftJoin('user_dividend_contract as top_uc', function ($join) {
                    $join->on('top_uc.user_id', 'users.top_id')->where('top_uc.status', '1');
                })
                ->leftJoin('user_group', 'user_group.id', 'users.user_group_id')
                ->leftJoin('user_type', 'user_type.id', 'users.user_type_id')
                ->leftJoin('user_profile as user_profile1', function ($join) {
                    $join->on('users.id', '=', 'user_profile1.user_id')
                        ->where('user_profile1.attribute', 'user_observe');
                })
                ->where($where)
                ->skip($start)->take($length);

            if ($include_all && $user_id > 0) {
                // 自定义排序
                $data['data'] = $data['data']->orderByRaw(DB::raw("CASE users.id WHEN {$user_id} THEN 0 ELSE users.id END asc"));
            } else {
                $data['data'] = $data['data']->orderBy("user_dividend_contract.user_id", 'ASC');
            }
            $data['data'] = $data['data']->get();
            if (!$data['data']->isEmpty()) {
                $data['data']->toArray();
            }

            // 获取平台派发最低用户层级
            $dividend_send_low_level = get_config('dividend_send_low_level');

            $api_dividend = new Dividend();

            //B线分红模式是否累计上期盈亏额
            $dividend_cumulative = get_config('dividend_mode', 0);
            foreach ($data['data'] as $_key => $val) {
                $_content = json_decode($val['content'], true);
                if ((empty($data['data'][$_key]['top_type']) || $data['data'][$_key]['top_type'] == 2) && $data['data'][$_key]['user_level'] <= $dividend_send_low_level) {
                    $data['data'][$_key]['top_type'] = 2;
                    $_content = $api_dividend->getDefaultContent(2, $data['data'][$_key]['user_level']);
                }

                if (!empty($_content)) {
                    $data['data'][$_key]['top_rate'] = max(array_column($_content, 'rate'));
                } else {
                    $data['data'][$_key]['top_rate'] = 0;
                }

                if (!isset($data['data'][$_key]['mode'])) {
                    $data['data'][$_key]['mode'] = $dividend_cumulative;
                }
            }

            return response()->json($data);
        }
    }

    /**
     * 获取契约调整记录
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postRecord(Request $request)
    {
        if ($request->ajax()) {
            $user_id = $request->get('user_id', 0);
            if (empty($user_id)) {
                return response()->json(array('status' => -1, 'msg' => '用户ID错误！'));
            }

            $data = UserDividendContract::select([
                'user_dividend_contract.id as id',
                'users.username',
                'user_dividend_contract.user_id',
                'user_dividend_contract.content',
                'user_dividend_contract.status',
                'user_dividend_contract.created_at',
                'user_dividend_contract.accept_at',
                'user_dividend_contract.delete_at',
                DB::raw("CASE WHEN user_dividend_contract.status=2 OR user_dividend_contract.status=3 THEN user_dividend_contract.delete_stage ELSE user_dividend_contract.created_stage END as stage"),
                DB::raw("CASE WHEN user_dividend_contract.status=2 OR user_dividend_contract.status=3 THEN user_dividend_contract.delete_username ELSE user_dividend_contract.created_username END as stage_username"),
            ])
                ->leftJoin('users', 'users.id', 'user_dividend_contract.user_id')
                ->where([
                    ['user_id', '=', $user_id]
                ])
                ->orderBy('user_dividend_contract.id', 'desc')
                ->get();

            if (!$data->isEmpty()) {
                $data = $data->toArray();
                foreach ($data as $key => $val) {
                    if (!empty($val['content'])) {
                        $data[$key]['content'] = json_decode($val['content'], true);
                    }
                }
            } else {
                return response()->json([
                    'status' => 1,
                    'msg' => '记录为空！',
                ]);
            }

            return response()->json([
                'status' => 0,
                'data' => $data,
            ]);
        }
    }

    /**
     * 签订新分红契约
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function getCreateoredit(Request $request)
    {
        $user_id = $request->get('user_id', 0);
        if (empty($user_id) || !is_numeric($user_id)) {
            return redirect("/dividend/")->withErrors('用户ID错误');
        }

        // 获取用户上级
        $_user = User::select([
            'id',
            'username',
            'parent_id',
            DB::raw('jsonb_array_length(users.parent_tree) as user_level'),
        ])
            ->where('id', $user_id)
            ->first();
        if (empty($_user)) {
            $_user = (object)[
                'id' => 0,
                'username' => '',
                'parent_id' => 0,
            ];
        }

        $apiDividend = new Dividend();
        $dividend = $apiDividend->getUserDividend($user_id, $_user->parent_id);

        if (!is_array($dividend)) {
            $err_msg = '用户ID错误！';
            if ($dividend === -1) {
                $err_msg = '用户上级未签订契约！';
            }
            return redirect("/dividend/")->withErrors($err_msg);
        }
        return view("dividend.create", [
            'dividend_type' => $dividend['dividend_type'],
            'parent_dividend' => $dividend['parent_dividend'] ?? [],
            'user_valid_dividend' => $dividend['user_valid_dividend'] ?? [],
            'user_unconfirmed_dividend' => $dividend['user_unconfirmed_dividend'] ?? [],
            'user' => $_user,
            'self_max_rate' => $dividend['self_max_rate'],
            'dividend_step' => $dividend['dividend_step'],
        ]);
    }

    /**
     * 编辑或签订新分红契约
     * 如果用户有正在确认或已生效契约则对契约修改，如果用户契约失效则重新签订新契约
     * @param ConfigCreateRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function postCreateoredit(Request $request)
    {
        $user_id = $request->get('user_id', 0);
        if (empty($user_id) || !is_numeric($user_id)) {
            return response()->json(array('status' => -1, 'msg' => '用户ID错误'));
        }

        $type = (int)$request->get('type', get_config('dividend_default_type', 2));
        $status = (int)$request->get('status', 0);

        if ($type == 1) {
            $user_dividend = [
                'type' => $type,
                'status' => $status,
            ];
        } else {
            $user_dividend = [
                'type' => $type,                                      // 分红类型
                'status' => $status,                                    // 状态
                'mode' => (int)$request->get('mode', 0),              // 分红模式
                'base_rate' => (int)$request->get('base_rate', 0),         // 分红比例
                'base_consume_day' => (int)$request->get('base_consume_day', 0),   // 分红要求消费天数：
                'base_min_day_sales' => (int)$request->get('base_min_day_sales', 0), // 每天最低日量
                'consume_type' => (int)$request->get('consume_type', 0),       // 消费量类型
                'loss_type' => (int)$request->get('loss_type', 0),          // 亏损量类型
                'reward_type' => (int)$request->get('reward_type', 0),        // 奖励类型
            ];

            $consume_amount = $request->get('consume_amount');  // 消费量
            $profits = $request->get('profit');          // 亏损量
            $daus = $request->get('daus');            // 有效会员
            $rates = $request->get('rate');            // 奖励金额
            foreach ($consume_amount as $key => $val) {
                $user_dividend['content'][] = [
                    'consume_amount' => is_numeric($consume_amount[$key]) ? $consume_amount[$key] : 0,
                    'profit' => is_numeric($profits[$key]) ? $profits[$key] : 0,
                    'daus' => is_numeric($daus[$key]) ? $daus[$key] : 0,
                    'rate' => is_numeric($rates[$key]) ? $rates[$key] : 0,
                ];
            }
        }

        // 获取用户上级
        $_user = User::select([
            'id',
            'username',
            'parent_id',
            DB::raw('jsonb_array_length(users.parent_tree) as user_level'),
        ])
            ->where('id', $user_id)
            ->first();
        if (empty($_user)) {
            return response()->json(['status' => 1, 'msg' => '用户不存在！']);
        }

        // 高于系统最高派发层级总代不可修改契约
        if ($_user->user_level < get_config('dividend_send_high_level', 0) && $_user->parent_id != 0) {
            return response()->json(['status' => 1, 'msg' => '对不起，用户层级高于系统最高派发层级，该总代不可签约！']);
        }

        $parent_id = $_user->parent_id;

        $dividend = new Dividend();
        $result = $dividend->setUserDividend($user_id, $parent_id, $user_dividend, false);

        return response()->json(['status' => ($result) ? 0 : 1, 'msg' => $dividend->err_msg]);
    }

    public function deleteIndex(Request $request)
    {
        $user_id = $request->get('user_id');

        if (!is_numeric($user_id)) {
            return redirect("/dividend/")->withErrors('上级ID错误');
        }

        $affected = UserDividendContract::leftJoin('users', 'users.id', 'user_dividend_contract.user_id')
            ->where(function ($query) use ($user_id) {
                $query->where('user_dividend_contract.status', '=', 1)
                    ->orWhere('user_dividend_contract.status', '=', 0);
            })
            ->where(function ($query) use ($user_id) {
                $query->where('users.parent_tree', '@>', $user_id)
                    ->orWhere('users.id', '=', $user_id);
            })
            ->update([
                'status' => 3,
                'delete_at' => Carbon::now(),
                'delete_stage' => 2,
                'delete_username' => auth()->user()->username,
            ]);

        if ($affected) {
            return redirect("/dividend/")->withSuccess('团队契约清除完成');
        } else {
            return redirect("/dividend/")->withErrors('清除失败');
        }
    }
}

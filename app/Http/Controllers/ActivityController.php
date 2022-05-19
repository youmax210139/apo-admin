<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ActivityCreateRequest;
use App\Http\Requests\ActivityUpdateRequest;
use Service\Models\Activity;
use Carbon\Carbon;
use Service\Models\ActivityRecord;
use Service\Models\ActivityReglink;
use Service\Models\ActivityWithdrawalDelay;
use Service\Models\PrizePool;
use Service\API\Activity\Reglink as APIReglink;
use Service\API\Activity\Withdrawaldelay as APIWithdrawaldelay;
use Service\API\Activity\Prizepool as APIPrizepool;
use Service\Models\User;
use Service\Models\UserLevel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Requests\ActivityRecordRequest;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    protected $fields = [
        'ident' => '',
        'name' => '',
        'start_time' => null,
        'end_time' => null,
        'config' => '{}',
        'config_ui' => '{}',
        'deny_user_group' => [],
        'allow_user_top_id' => [],
        'allow_user_level_id' => [],
        'summary' => '',
        'description' => '',
        'status' => 0,
        'draw_method' => 1,
        'sort' => 1,
    ];

    public function getIndex()
    {
        return view('activity.index');
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

            $data['recordsTotal'] = Activity::all()->count();

            $data['recordsFiltered'] = $data['recordsTotal'];
            $data['data'] = Activity::select()
                ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                ->skip($start)->take($length)
                ->get();

            if ($data['data']) {
                $now = (string)Carbon::now();
                foreach ($data['data'] as $k => $v) {
                    $data['data'][$k]['process_status'] = $v->status ?
                        (($now > $v->start_time && $now < $v->end_time) ? '进行中' :
                            (($now < $v->start_time) ? '将来进行' : '已过期')) : '关闭';
                }
            }

            return response()->json($data);
        }
    }

    /**
     * 新建活动
     *
     * @return \Illuminate\Http\Response
     */
    public function getCreate()
    {
        $data = ['deny_modify' => 0];
        foreach ($this->fields as $field => $default) {
            $data[$field] = old($field, $default);
        }

        //获取所有总代
        $data['top_users'] = User::select(['id', 'username'])->where('parent_id', 0)->orderBy('username', 'asc')->get();

        // 获取用户分层
        $data['user_level'] = UserLevel::select(['id', 'name'])->orderBy('id', 'asc')->get();

        return view('activity.create', $data);
    }

    /**
     * 新建后台设置项.
     *
     * @param ActivityCreateRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(ActivityCreateRequest $request)
    {
        $Activity = new Activity();
        $input = $request->all();
        foreach (array_keys($this->fields) as $field) {
            if (in_array($field, array('deny_user_group', 'allow_user_top_id', 'allow_user_level_id'))) {
                if (isset($input[$field])) {
                    $Activity->$field = json_encode($input[$field], JSON_NUMERIC_CHECK);
                }
            } else {
                $Activity->$field = $request->get($field, $this->fields[$field]);
            }
        }

        $Activity->config = $this->putConfigField($Activity->config);           //配置字段特殊处理
        $Activity->config_ui = $this->putConfigField($Activity->config_ui);
        $Activity->ident = strtolower($Activity->ident);

        $Activity->save();

        return redirect('/Activity\/')->withSuccess('添加成功');
    }

    /**
     * 编辑页面
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function getEdit(Request $request)
    {
        $debug = $request->get('debug', 0);         //1 开启json显示模式

        //只有超级管理员可以修改活动标识和发放方式
        $deny_modify = (app()->environment() == 'production' && auth()->id() != 1) ? 1 : 0;

        $id = (int)$request->get('id', 0);
        $Activity = Activity::find($id);
        if (!$Activity) {
            return redirect('/Activity\/')->withErrors("找不到该权限");
        }
        $data = ['id' => $id, 'deny_modify' => $deny_modify];
        foreach (array_keys($this->fields) as $field) {
            if (in_array($field, array('deny_user_group', 'allow_user_top_id', 'allow_user_level_id'))) {
                $data[$field] = old($field, json_decode($Activity->$field, true));
            } else {
                $data[$field] = old($field, $Activity->$field);
            }
        }

        $data['config'] = $this->getConfigField($data['config'], $debug);   //配置字段特殊处理

        //获取总代限制
        $data['top_users'] = User::select(['id', 'username'])->where('parent_id', 0)->orderBy('username', 'asc')->get();
        if (!empty($data['allow_user_top_id'])) {
            foreach ($data['top_users'] as $item) {
                if (in_array($item->id, $data['allow_user_top_id'])) {
                    $item->checked = true;
                }
            }
        }

        //获取用户分层限制
        $data['user_level'] = UserLevel::select(['id', 'name'])->orderBy('id', 'asc')->get();
        if (!empty($data['allow_user_level_id'])) {
            foreach ($data['user_level'] as $item) {
                if (in_array($item->id, $data['allow_user_level_id'])) {
                    $item->checked = true;
                }
            }
        }

        return view('activity.edit', $data);
    }

    //显示配置字段特殊处理
    private function getConfigField($json, $debug)
    {
        if (is_array($json)) {
            $tmp = $json;
        } else {
            $tmp = json_decode($json, true);
        }

        return isset($tmp['config']) && $debug == 0 ? $tmp : $json;
    }

    //保存配置字段特殊处理
    private function putConfigField($json)
    {
        if (is_array($json)) {
            return json_encode($json);
        }

        @json_decode($json);
        if (json_last_error() === JSON_ERROR_NONE) {
            return $json;
        }

        return '{}';
    }

    /**
     * 更新活动
     *
     * @param ActivityUpdateRequest $request
     * @return mixed
     */
    public function putEdit(ActivityUpdateRequest $request)
    {
        $id = (int)$request->get('id', 0);
        $Activity = Activity::find($id);
        $input = $request->all();
        foreach (array_keys($this->fields) as $field) {
            if (in_array($field, array('deny_user_group', 'allow_user_top_id', 'allow_user_level_id'))) {
                if (isset($input[$field])) {
                    $Activity->$field = json_encode($input[$field], JSON_NUMERIC_CHECK);
                } else {
                    $Activity->$field = '[]';
                }
            } else {
                $Activity->$field = $request->get($field, $this->fields[$field]);
            }
        }

        $Activity->config = $this->putConfigField($Activity->config);           //配置字段特殊处理
        $Activity->config_ui = $this->putConfigField($Activity->config_ui);
        $Activity->ident = strtolower($Activity->ident);

        if ($Activity->save()) {
            return redirect('/Activity\/')->withSuccess('修改成功');
        } else {
            return redirect('/Activity\/')->withErrors('修改失败');
        }
    }

    /**
     * 设置活动为启用或者禁用
     *
     * @param Request $request
     * @return mixed
     */
    public function putStatus(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $Activity = Activity::find($id);
        $Activity->status = $Activity->status == 0;
        $tips = ($Activity->status == 1) ? '启用' : '禁用';

        if ($Activity->save()) {
            return redirect('/Activity\/')->withSuccess("修改活动【{$Activity->name}】为【{$tips}】");
        } else {
            return redirect('/Activity\/')->withErrors("更新活动失败");
        }
    }

    public function getRecord(Request $request)
    {
        $data['start_date'] = Carbon::yesterday();
        $data['end_date'] = Carbon::tomorrow();
        $data['id'] = (int)$request->get('id', 0);
        $data['activity'] = Activity::find($data['id']);
        if (empty($data['activity'])) {
            return redirect('/Activity\/')->withErrors("活动不存在或者已经删除");
        }
        return view('activity.record', $data);
    }

    /**
     * 中奖名单
     * @param Request $request
     */
    public function postRecord(ActivityRecordRequest $request)
    {

        $id = (int)$request->get('id', 0);
        $activity = Activity::find($id);
        if (empty($activity)) {
            return redirect('/Activity\/')->withErrors("活动不存在或者已经删除");
        }
        $data = [];
        $data['draw'] = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $order = $request->get('order');
        $columns = $request->get('columns');

        $export = (int)$request->get('export', 0);
        $param = [];
        $param['username'] = $request->get('username', '');//用户名
        $param['ip'] = (string)$request->get('ip', '');//用户IP
        $param['start_date'] = $request->get('start_date');//开始时间
        $param['end_date'] = $request->get('end_date');//开始时间
        $param['amount_min'] = $request->get('amount_min');//金额大小
        $param['amount_max'] = $request->get('amount_max');//金额大小
        // 验证IP 合法性
        if (!filter_var($param['ip'], FILTER_VALIDATE_IP)) {
            $param['ip'] = '';
        }
        //查询条件
        $where = function ($query) use ($param) {
            if (!empty($param['username'])) {
                $query->where('users.username', $param['username']);
            }
            if (!empty($param['ip'])) {
                $query->where('activity_record.ip', $param['ip']);
            }
            //下单时间对比
            if (!empty($param['start_date'])) {
                $query->where('activity_record.record_time', '>=', $param['start_date']);
            }
            if (!empty($param['end_date'])) {
                $query->where('activity_record.record_time', '<=', $param['end_date']);
            }
            //投注金额对比
            if (!empty($param['amount_min'])) {
                $query->where('activity_record.draw_money', '>=', $param['amount_min']);
            }
            if (!empty($param['amount_max'])) {
                $query->where('activity_record.draw_money', '<=', $param['amount_max']);
            }
        };
        $data['recordsTotal'] = $data['recordsFiltered'] = ActivityRecord::leftJoin('users', 'users.id', 'activity_record.user_id')
            ->where($where)->where('activity_id', $id)->count();
        $file_name = date('Ymd-H_i_s') . "-{$activity->name}-中奖名单.csv";
        $data['data'] = ActivityRecord::select(['activity_record.*', 'users.username'])
            ->leftJoin('users', 'users.id', 'activity_record.user_id')
            ->where($where)
            ->where('activity_id', $id);
        if (empty($export)) {
            $data['data'] = $data['data']->skip($start)->take($length)
                ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                ->get();
            foreach ($data['data'] as &$v) {
                $v->activity_name = $activity->name;
            }
            return response()->json($data);
        } else {
            $response = new StreamedResponse(null, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
            ]);
            $query = $data['data'];
            $draw_method = $activity['draw_method'];
            $response->setCallback(function () use ($query, &$draw_method) {
                $out = fopen('php://output', 'w');
                fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // 添加 BOM
                $first = true;
                $query->chunk(500, function ($results) use (&$first, &$draw_method, $out) {
                    if ($first) {
                        //列名
                        $columnNames[] = '活动ID';
                        $columnNames[] = '用户名';
                        $columnNames[] = '领取时间';
                        $columnNames[] = '领取金额';
                        if ($draw_method == 1) {
                            $columnNames[] = '管理员IP';
                        } elseif ($draw_method == 0) {
                            $columnNames[] = '领取IP';
                        } else {
                            $columnNames[] = 'IP';
                        }
                        $columnNames[] = '状态';
                        fputcsv($out, $columnNames);
                        $first = false;
                    }
                    $datas = [];
                    foreach ($results as $item) {
                        $datas[] = [
                            $item->activity_id,
                            $item->username,
                            $item->record_time,
                            $item->draw_money,
                            $item->ip,
                            $item->status,
                        ];
                    }
                    foreach ($datas as $item) {
                        fputcsv($out, $item);
                    }
                });
                fclose($out);
            });
            $response->send();
        }
    }

    /**
     * 邀请码注册统计页
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getReglink(Request $request)
    {
        $data['start_date'] = Carbon::today()->subDays(10);
        $data['end_date'] = Carbon::today();
        return view('activity.reglink', $data);
    }

    /**
     * 邀请码注册统计页
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function postReglink(Request $request)
    {
        if ($request->ajax()) {
            $data = array();
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');

            //查询条件
            $param = [];
            $param['start_date'] = $request->get('start_date');    //开始时间
            $param['end_date'] = $request->get('end_date');      //结束时间
            $param['username'] = $request->get('username');
            $param['status'] = $request->get('status');

            $where = function ($query) use ($param) {
                if (!empty($param['start_date'])) {
                    $query->where('activity_reglink.start_time', '>=', $param['start_date']);
                }
                if (!empty($param['end_date'])) {
                    $query->where('activity_reglink.start_time', '<=', $param['end_date']);
                }
                if ($param['status'] != '') {
                    $query->where('activity_reglink.status', $param['status']);
                }

                //用户查询条件
                if (!empty($param['username'])) {
                    $query->where('users.username', $param['username']);
                }
            };

            $data['recordsTotal'] = $data['recordsFiltered'] =
                ActivityReglink::leftJoin('users', 'users.id', 'activity_reglink.user_id')->where($where)->count();

            $data['data'] = ActivityReglink::select([
                'activity_reglink.id',
                'users.username',
                'activity_reglink.count',
                'activity_reglink.count_step',
                'activity_reglink.prize',
                'activity_reglink.start_time',
                'activity_reglink.end_time',
                'activity_reglink.created_at',
                'activity_reglink.status',
                'admin_users.username AS verified_admin',
                'activity_reglink.verified_at',
                'activity_reglink.comment',
            ])
                ->leftJoin('users', 'users.id', 'activity_reglink.user_id')
                ->leftJoin('admin_users', 'activity_reglink.verified_admin_user_id', 'admin_users.id')
                ->where($where)
                ->skip($start)->take($length)
                ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                ->get();

            return response()->json($data);
        }
    }

    /**
     * 邀请码活动的审核
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function putVerify(Request $request)
    {
        $reglink_id = (int)$request->get('id', 0);
        $row = ActivityReglink::where('id', $reglink_id)->first();
        if (!$row) {
            return response()->json(array('status' => -1, 'msg' => '数据错误！'));
        }
        if (in_array($row->status, [1, 2])) {
            return response()->json(array('status' => 1, 'msg' => '该笔数据已处理！'));
        }

        $status = (int)$request->get('status', 0);
        $comment = $request->get('comment', '');

        $row->status = $status;
        $row->comment = $comment;
        $row->verified_admin_user_id = auth()->id();
        $row->verified_at = (string)Carbon::now();

        DB::beginTransaction();
        if ($status == 2) {
            $api_reglink = new APIReglink();
            if (!$api_reglink->draw($reglink_id)) {
                DB::rollBack();
                return response()->json(array('status' => 1, 'msg' => '审核失败！'));
            }
        }
        if (!$row->save()) {
            DB::rollBack();
            return response()->json(array('status' => 1, 'msg' => '审核失败！'));
        }
        DB::commit();
        return response()->json(array('status' => 0, 'msg' => '审核成功！'));
    }

    /**
     * 提款补偿金审核列表
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getWithdrawalDelay(Request $request)
    {
        $data['start_date'] = Carbon::today()->subDays(10);
        $data['end_date'] = Carbon::today()->endOfDay();
        return view('activity.withdrawal_delay', $data);
    }

    /**
     * 提款补偿金审核列表
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function postWithdrawalDelay(Request $request)
    {
        if ($request->ajax()) {
            $data = array();
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');

            //查询条件
            $param = [];
            $param['start_date'] = $request->get('start_date');    //开始时间
            $param['end_date'] = $request->get('end_date');        //结束时间
            $param['username'] = $request->get('username');
            $param['status'] = $request->get('status');

            $where = function ($query) use ($param) {
                if ($param['start_date']) {
                    $query->where('activity_withdrawal_delay.created_at', '>=', $param['start_date']);
                }
                if ($param['end_date']) {
                    $query->where('activity_withdrawal_delay.created_at', '<=', $param['end_date']);
                }
                if ($param['status']) {
                    $query->where('activity_withdrawal_delay.status', $param['status']);
                }

                //用户查询条件
                if ($param['username']) {
                    $query->where('users.username', $param['username']);
                }
            };

            $data['recordsTotal'] = $data['recordsFiltered'] =
                ActivityWithdrawalDelay::leftJoin('users', 'users.id', 'activity_withdrawal_delay.user_id')->where($where)->count();

            $data['data'] = ActivityWithdrawalDelay::select([
                'activity_withdrawal_delay.id',
                'users.username',
                'activity_withdrawal_delay.withdrawal_id',
                'withdrawals.created_at AS apply_at',
                'withdrawals.done_at',
                'activity_withdrawal_delay.amount',
                'activity_withdrawal_delay.delay_minutes',
                'activity_withdrawal_delay.percent',
                'activity_withdrawal_delay.prize',
                'activity_withdrawal_delay.created_at',
                'activity_withdrawal_delay.status',
                'admin_users.username AS verified_admin',
                'activity_withdrawal_delay.verified_at',
                'activity_withdrawal_delay.comment',
            ])
                ->leftJoin('users', 'users.id', 'activity_withdrawal_delay.user_id')
                ->leftJoin('admin_users', 'activity_withdrawal_delay.verified_admin_user_id', 'admin_users.id')
                ->leftJoin('withdrawals', 'activity_withdrawal_delay.withdrawal_id', 'withdrawals.id')
                ->where($where)
                ->skip($start)->take($length)
                ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                ->get();

            return response()->json($data);
        }
    }

    /**
     * 提款补偿金审核
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function putWithdrawalDelayVerify(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $row = ActivityWithdrawalDelay::where('id', $id)->first();
        if (!$row) {
            return response()->json(array('status' => -1, 'msg' => '数据错误！'));
        }
        if ($row->status) {
            return response()->json(array('status' => 1, 'msg' => '该笔数据已处理！'));
        }

        $activity = Activity::where('id', $row->activity_id)->where('status', 1)->first();
        if (empty($activity)) {
            return response()->json(array('status' => 1, 'msg' => '该活动已被禁用！'));
        }

        $status = (int)$request->get('status', 0);
        $comment = $request->get('comment', '');

        $row->status = $status == 2 ? 2 : 1;
        $row->comment = $comment;
        $row->verified_admin_user_id = auth()->id();
        $row->verified_at = (string)Carbon::now();

        DB::beginTransaction();
        if ($status == 2) {
            $api_delay = new APIWithdrawaldelay();
            if (!$api_delay->draw($row)) {
                DB::rollBack();
                return response()->json(array('status' => 1, 'msg' => '审核失败！'));
            }
        }
        if (!$row->save()) {
            DB::rollBack();
            return response()->json(array('status' => 1, 'msg' => '审核失败！'));
        }
        DB::commit();
        return response()->json(array('status' => 0, 'msg' => '审核成功！'));
    }

    /**
     * 奖池活动管理页
     */
    public function getPrizepool()
    {
        return view('activity.prizepool');
    }

    /**
     * 奖池活动管理页
     */
    public function postPrizepool(Request $request)
    {
        if ($request->ajax()) {
            $data = array();
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');

            $data['recordsTotal'] = $data['recordsFiltered'] = PrizePool::count();

            $data['data'] = PrizePool::select([
                'prize_pool.*',
                DB::raw("prize_pool.data->>'total_prize' as total_prize"),
                DB::raw("prize_pool.data->>'prize' as prize"),
                'admin_users.username AS verified_admin'
            ])
                ->leftJoin('admin_users', 'prize_pool.verified_admin_user_id', 'admin_users.id')
                ->skip($start)->take($length)
                ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                ->get();

            foreach ($data['data'] as $k => $v) {
                $userlist = '';
                $json = json_decode($v['data']);
                if ($json->list) {
                    foreach ($json->list as $user) {
                        $userlist = $userlist . $user->username . ",";
                    }
                    $data['data'][$k]['userlist'] = substr($userlist, 0, strlen($userlist) - 1);
                } else {
                    $data['data'][$k]['userlist'] = '';
                }
            }

            return response()->json($data);
        }
    }

    /**
     * 奖池活动的审核
     */
    public function putPrizepoolverify(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $row = PrizePool::where('id', $id)->first();
        if (!$row) {
            return response()->json(array('status' => -1, 'msg' => '数据错误！'));
        }

        if ($row->frozen != 1) {
            return response()->json(array('status' => 1, 'msg' => '非法请求！'));
        }

        if (in_array($row->status, [1, 2])) {
            return response()->json(array('status' => 1, 'msg' => '该笔数据已处理！'));
        }

        $status = (int)$request->get('status', 0);
        if (!in_array($status, [1, 2])) {
            return response()->json(array('status' => -1, 'msg' => '数据错误！'));
        }

        $row->status = $status;
        $row->verified_admin_user_id = auth()->id();
        $row->verified_at = (string)Carbon::now();

        DB::beginTransaction();
        if ($status == 1) {
            $api_prizepool = new APIPrizepool();
            if (!$api_prizepool->verify($row)) {
                DB::rollBack();
                return response()->json(array('status' => 1, 'msg' => '审核失败了！'));
            }
        }
        if (!$row->save()) {
            DB::rollBack();
            return response()->json(array('status' => 1, 'msg' => '审核失败！'));
        }
        DB::commit();
        return response()->json(array('status' => 0, 'msg' => '审核成功！'));
    }

    /**
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function getSendaward(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $activity = Activity::find($id);
        if (empty($activity)) {
            return redirect()->back()->withErrors("活动不存在或者已经删除");
        }
        if ($activity->draw_method != 1) {
            return redirect()->back()->withErrors("活动领取类型不正确");
        }
        return view('activity.sendaward', ['activity' => $activity]);
    }

    /*
     * 发放礼金
     */
    public function postSendaward(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $money = (float)$request->get('money', 0);
        $award_user = $request->get('award_user', '');
        $desc = $request->get('description', '');
        if (empty($award_user)) {
            return redirect()->back()->withErrors("请输入发放用户名");
        }
        if (empty($money) || !is_numeric($money)) {
            return redirect()->back()->withErrors("请输入礼金");
        }
        if (empty($desc)) {
            return redirect()->back()->withErrors("请输入备注");
        }
        $activity = Activity::find($id);
        if (empty($activity)) {
            return redirect()->back()->withErrors("活动不存在或者已经删除");
        }
        if ($activity->draw_method != 1) {
            return redirect()->back()->withErrors("活动领取类型不正确");
        }
        $award_user = explode(',', $award_user);
        $users = User::select(['id', 'username'])->whereIn('username', $award_user)->get();
        $success = [];
        //逐条发放
        foreach ($users as $user) {
            $second_verify = new \Service\Models\SecondVerifyList();
            $second_verify->user_id = $user->id;
            $second_verify->created_admin_id = Auth()->id();
            $second_verify->verify_type = 'activity';
            $data = [
                'activity_id' => $id,
                'user_id' => $user->id,
                'award' => $money,
                "ordertype" => "CXCZ",
                "description" => $activity->name . '|' . $desc,
                "ordertypetext" => "促销充值"
            ];

            $second_verify->data = json_encode($data);
            if (!$second_verify->save()) {
                continue;
            }
            $success[] = $user->username;
        }
        if (empty($success)) {
            return redirect()->back()->withErrors("操作失败,用户不存在");
        }
        return redirect()->back()->withSuccess("用户" . implode(',', $success) . "操作成功，联系风控审核后生效");
    }

    /**
     * 幸运大奖池
     * @param Request $request
     */
    public function getJackpot(Request $request)
    {
        $type = $request->get('type', '');
        switch ($type) {
            case 'no_prize':
                $period = $request->get('period', '');
                $period_info = \Service\Models\JackpotPeriod::where('period', $period)->first();
                if (empty($period_info)) {
                    return redirect()->back()->withErrors("{$period} 期不存在");
                }
                if ($period_info->code_status !== 0) {
                    return redirect()->back()->withErrors("只能修改未开奖记录");
                }
                $period_info->code_status = 2;
                $period_info->save();
                return redirect()->back()->withSuccess("{$period_info->period}修改成功");
                break;
            case 'period_draw':
                $period = $request->get('period', '');
                $jackpot = new \Service\API\Activity\Jackpot();
                if ($jackpot->draw($period)) {
                    return redirect()->back()->withSuccess("{$period}期人工开奖成功");
                } else {
                    return redirect()->back()->withErrors("{$jackpot->ret_msg}");
                }
                break;
            case 'operation_prize':
                $period = $request->get('period', '');
                $period_info = \Service\Models\JackpotPeriod::where('period', $period)->first();
                if (empty($period_info)) {
                    return redirect()->back()->withErrors("{$period} 期不存在");
                }
                $now_date_time = Carbon::now()->toDateTimeString();
                if ($period_info->end_at <= $now_date_time) {
                    return redirect()->back()->withErrors("{$period} 期于 {$period_info->end_at} 结束了，不允许增减金额");
                }
                return view('activity.jackpot_operation_prize', ['period_info' => $period_info]);
                break;
            case 'user_code':
                $period = $request->get('period', '');
                $period_info = \Service\Models\JackpotPeriod::where('period', $period)->first();
                if (empty($period_info)) {
                    return redirect()->back()->withErrors("{$period} 期不存在");
                }
                return view('activity.jackpot_user_code', ['period_info' => $period_info]);
                break;
            case 'period_postpone': //延期
                $period = $request->get('period', '');
                $period_info = \Service\Models\JackpotPeriod::where('period', $period)->first();
                if (empty($period_info)) {
                    return redirect()->back()->withErrors("{$period} 期不存在");
                }
                $next_sunday_date = Carbon::parse($period_info->end_at)->addDay(1)->endOfWeek()->toDateString();
                $check_period = \Service\Models\JackpotPeriod::where('start_at', '<', $next_sunday_date)
                    ->where('end_at', '>', $next_sunday_date)
                    ->first();
                if ($check_period) {
                    return redirect()->back()->withErrors("已存相同时间的期号：{$check_period->period}，开始{$check_period->start_at}，结束{$check_period->end_at}");
                }
                $period_info->end_at = $next_sunday_date . ' 23:59:59';
                if ($period_info->save()) {
                    return redirect()->back()->withSuccess("{$period_info->period} 延期一周成功，新的结束时间：{$period_info->end_at}");
                } else {
                    return redirect()->back()->withErrors("{$period_info->period} 延期一周失败");
                }
                break;
            case 'period_delete': //删除
                $period = $request->get('period', '');
                $period_info = \Service\Models\JackpotPeriod::where('period', $period)->first();
                if (empty($period_info)) {
                    return redirect()->back()->withErrors("{$period} 期不存在");
                }
                $now_date_time = Carbon::now()->toDateString();
                if ($period_info->end_at < $now_date_time) {
                    return redirect()->back()->withErrors("{$period} 期已结束，禁止删除");
                }
                //检查用户领取号码
                $check_user_codes = \Service\Models\JackpotUserCode::where('period', $period)->first();
                if ($check_user_codes) {
                    return redirect()->back()->withErrors("{$period} 期已有用户领取了抽奖号码，禁止删除");
                }
                //设置超时时间
                set_time_limit(200);
                \Service\Models\JackpotPeriod::where('period', $period)->delete();
                //删除号码库
                \Service\Models\JackpotPeriodCodeData::where('period', $period)->delete();
                return redirect()->back()->withSuccess("{$period_info->period} 删除成功");
                break;
            case 'periods':
            default:
                return view('activity.jackpot_period');
        }
    }

    public function postJackpot(Request $request)
    {
        $type = $request->get('type', '');
        switch ($type) {
            case 'operation_prize': //增加奖金
                $period = $request->get('period', '');
                $period_info = \Service\Models\JackpotPeriod::where('period', $period)->first();
                if (empty($period_info)) {
                    return redirect()->back()->withErrors("{$period} 期不存在");
                }
                $operation_prize = $request->get('operation_prize', 0);
                if (!is_numeric($operation_prize)) {
                    return redirect()->back()->withErrors("输入必须为数字");
                }
                $period_info->operation_prize = $operation_prize;
                $period_info->save();
                return redirect('/activity/jackpot\/')->withSuccess('【' . $period_info->period . '】增减金额修改成功');
            case 'user_code': //用户号码列表
                $data = [];
                $data['draw'] = $request->get('draw');
                $period = $request->get('period', '');
                $prize = $request->get('przie', '');
                $start = $request->get('start');
                $length = $request->get('length');
                $search = $request->get('search');

                $where_array = [];
                $where_array[] = ['jackpot_user_code.period', '=', $period];
                if ($prize) {
                    $where_array[] = ['jackpot_user_code.prize_level', '>', 0];
                }

                $data['recordsTotal'] = \Service\Models\JackpotUserCode::where($where_array)->count();

                if (strlen($search['value']) > 0) {
                    $data['recordsFiltered'] = \Service\Models\JackpotUserCode::leftJoin('users', 'users.id', 'jackpot_user_code.user_id')
                        ->where($where_array)
                        ->where(function ($query) use ($search) {
                            if ($search['value'] == '中奖') {
                                $query->where('jackpot_user_code.prize_level', '>', 0);
                            } elseif ($search['value'] == '一等奖') {
                                $query->where('jackpot_user_code.prize_level', '=', 1);
                            } elseif ($search['value'] == '二等奖') {
                                $query->where('jackpot_user_code.prize_level', '=', 2);
                            } else {
                                $query->where('users.username', 'LIKE', '%' . $search['value'] . '%');
                            }
                        })
                        ->count();

                    $data['data'] = \Service\Models\JackpotUserCode::select([
                        'jackpot_user_code.id',
                        'jackpot_user_code.period',
                        'users.username',
                        'jackpot_user_code.code',
                        'jackpot_user_code.created_at',
                        'jackpot_user_code.current_total_bet',
                        'jackpot_user_code.prize_level',
                    ])
                        ->leftJoin('users', 'users.id', 'jackpot_user_code.user_id')
                        ->where($where_array)
                        ->where(function ($query) use ($search) {
                            if ($search['value'] == '中奖') {
                                $query->where('jackpot_user_code.prize_level', '>', 0);
                            } elseif ($search['value'] == '一等奖') {
                                $query->where('jackpot_user_code.prize_level', '=', 1);
                            } elseif ($search['value'] == '二等奖') {
                                $query->where('jackpot_user_code.prize_level', '=', 2);
                            } else {
                                $query->where('users.username', 'LIKE', '%' . $search['value'] . '%');
                            }
                        })
                        ->skip($start)
                        ->take($length)
                        ->orderBy('id', 'desc')
                        ->get();
                } else {
                    $data['recordsFiltered'] = $data['recordsTotal'];
                    $data['data'] = \Service\Models\JackpotUserCode::select([
                        'jackpot_user_code.id',
                        'jackpot_user_code.period',
                        'users.username',
                        'jackpot_user_code.code',
                        'jackpot_user_code.created_at',
                        'jackpot_user_code.current_total_bet',
                        'jackpot_user_code.prize_level',
                    ])
                        ->leftJoin('users', 'users.id', 'jackpot_user_code.user_id')
                        ->where($where_array)
                        ->skip($start)
                        ->take($length)
                        ->orderBy('id', 'desc')
                        ->get();
                }
                return response()->json($data);
            case 'periods':
            default:
                $data = [];
                $data['draw'] = $request->get('draw');
                $start = $request->get('start');
                $length = $request->get('length');

                $data['recordsTotal'] = $data['recordsFiltered'] = \Service\Models\JackpotPeriod::count();

                $data['data'] = \Service\Models\JackpotPeriod::skip($start)
                    ->take($length)
                    ->orderBy('period', 'desc')
                    ->get();

                return response()->json($data);
        }
    }
}

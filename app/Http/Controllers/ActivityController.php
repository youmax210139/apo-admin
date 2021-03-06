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
                        (($now > $v->start_time && $now < $v->end_time) ? '?????????' :
                            (($now < $v->start_time) ? '????????????' : '?????????')) : '??????';
                }
            }

            return response()->json($data);
        }
    }

    /**
     * ????????????
     *
     * @return \Illuminate\Http\Response
     */
    public function getCreate()
    {
        $data = ['deny_modify' => 0];
        foreach ($this->fields as $field => $default) {
            $data[$field] = old($field, $default);
        }

        //??????????????????
        $data['top_users'] = User::select(['id', 'username'])->where('parent_id', 0)->orderBy('username', 'asc')->get();

        // ??????????????????
        $data['user_level'] = UserLevel::select(['id', 'name'])->orderBy('id', 'asc')->get();

        return view('activity.create', $data);
    }

    /**
     * ?????????????????????.
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

        $Activity->config = $this->putConfigField($Activity->config);           //????????????????????????
        $Activity->config_ui = $this->putConfigField($Activity->config_ui);
        $Activity->ident = strtolower($Activity->ident);

        $Activity->save();

        return redirect('/Activity\/')->withSuccess('????????????');
    }

    /**
     * ????????????
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function getEdit(Request $request)
    {
        $debug = $request->get('debug', 0);         //1 ??????json????????????

        //????????????????????????????????????????????????????????????
        $deny_modify = (app()->environment() == 'production' && auth()->id() != 1) ? 1 : 0;

        $id = (int)$request->get('id', 0);
        $Activity = Activity::find($id);
        if (!$Activity) {
            return redirect('/Activity\/')->withErrors("??????????????????");
        }
        $data = ['id' => $id, 'deny_modify' => $deny_modify];
        foreach (array_keys($this->fields) as $field) {
            if (in_array($field, array('deny_user_group', 'allow_user_top_id', 'allow_user_level_id'))) {
                $data[$field] = old($field, json_decode($Activity->$field, true));
            } else {
                $data[$field] = old($field, $Activity->$field);
            }
        }

        $data['config'] = $this->getConfigField($data['config'], $debug);   //????????????????????????

        //??????????????????
        $data['top_users'] = User::select(['id', 'username'])->where('parent_id', 0)->orderBy('username', 'asc')->get();
        if (!empty($data['allow_user_top_id'])) {
            foreach ($data['top_users'] as $item) {
                if (in_array($item->id, $data['allow_user_top_id'])) {
                    $item->checked = true;
                }
            }
        }

        //????????????????????????
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

    //??????????????????????????????
    private function getConfigField($json, $debug)
    {
        if (is_array($json)) {
            $tmp = $json;
        } else {
            $tmp = json_decode($json, true);
        }

        return isset($tmp['config']) && $debug == 0 ? $tmp : $json;
    }

    //??????????????????????????????
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
     * ????????????
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

        $Activity->config = $this->putConfigField($Activity->config);           //????????????????????????
        $Activity->config_ui = $this->putConfigField($Activity->config_ui);
        $Activity->ident = strtolower($Activity->ident);

        if ($Activity->save()) {
            return redirect('/Activity\/')->withSuccess('????????????');
        } else {
            return redirect('/Activity\/')->withErrors('????????????');
        }
    }

    /**
     * ?????????????????????????????????
     *
     * @param Request $request
     * @return mixed
     */
    public function putStatus(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $Activity = Activity::find($id);
        $Activity->status = $Activity->status == 0;
        $tips = ($Activity->status == 1) ? '??????' : '??????';

        if ($Activity->save()) {
            return redirect('/Activity\/')->withSuccess("???????????????{$Activity->name}?????????{$tips}???");
        } else {
            return redirect('/Activity\/')->withErrors("??????????????????");
        }
    }

    public function getRecord(Request $request)
    {
        $data['start_date'] = Carbon::yesterday();
        $data['end_date'] = Carbon::tomorrow();
        $data['id'] = (int)$request->get('id', 0);
        $data['activity'] = Activity::find($data['id']);
        if (empty($data['activity'])) {
            return redirect('/Activity\/')->withErrors("?????????????????????????????????");
        }
        return view('activity.record', $data);
    }

    /**
     * ????????????
     * @param Request $request
     */
    public function postRecord(ActivityRecordRequest $request)
    {

        $id = (int)$request->get('id', 0);
        $activity = Activity::find($id);
        if (empty($activity)) {
            return redirect('/Activity\/')->withErrors("?????????????????????????????????");
        }
        $data = [];
        $data['draw'] = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $order = $request->get('order');
        $columns = $request->get('columns');

        $export = (int)$request->get('export', 0);
        $param = [];
        $param['username'] = $request->get('username', '');//?????????
        $param['ip'] = (string)$request->get('ip', '');//??????IP
        $param['start_date'] = $request->get('start_date');//????????????
        $param['end_date'] = $request->get('end_date');//????????????
        $param['amount_min'] = $request->get('amount_min');//????????????
        $param['amount_max'] = $request->get('amount_max');//????????????
        // ??????IP ?????????
        if (!filter_var($param['ip'], FILTER_VALIDATE_IP)) {
            $param['ip'] = '';
        }
        //????????????
        $where = function ($query) use ($param) {
            if (!empty($param['username'])) {
                $query->where('users.username', $param['username']);
            }
            if (!empty($param['ip'])) {
                $query->where('activity_record.ip', $param['ip']);
            }
            //??????????????????
            if (!empty($param['start_date'])) {
                $query->where('activity_record.record_time', '>=', $param['start_date']);
            }
            if (!empty($param['end_date'])) {
                $query->where('activity_record.record_time', '<=', $param['end_date']);
            }
            //??????????????????
            if (!empty($param['amount_min'])) {
                $query->where('activity_record.draw_money', '>=', $param['amount_min']);
            }
            if (!empty($param['amount_max'])) {
                $query->where('activity_record.draw_money', '<=', $param['amount_max']);
            }
        };
        $data['recordsTotal'] = $data['recordsFiltered'] = ActivityRecord::leftJoin('users', 'users.id', 'activity_record.user_id')
            ->where($where)->where('activity_id', $id)->count();
        $file_name = date('Ymd-H_i_s') . "-{$activity->name}-????????????.csv";
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
                fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // ?????? BOM
                $first = true;
                $query->chunk(500, function ($results) use (&$first, &$draw_method, $out) {
                    if ($first) {
                        //??????
                        $columnNames[] = '??????ID';
                        $columnNames[] = '?????????';
                        $columnNames[] = '????????????';
                        $columnNames[] = '????????????';
                        if ($draw_method == 1) {
                            $columnNames[] = '?????????IP';
                        } elseif ($draw_method == 0) {
                            $columnNames[] = '??????IP';
                        } else {
                            $columnNames[] = 'IP';
                        }
                        $columnNames[] = '??????';
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
     * ????????????????????????
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
     * ????????????????????????
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

            //????????????
            $param = [];
            $param['start_date'] = $request->get('start_date');    //????????????
            $param['end_date'] = $request->get('end_date');      //????????????
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

                //??????????????????
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
     * ????????????????????????
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function putVerify(Request $request)
    {
        $reglink_id = (int)$request->get('id', 0);
        $row = ActivityReglink::where('id', $reglink_id)->first();
        if (!$row) {
            return response()->json(array('status' => -1, 'msg' => '???????????????'));
        }
        if (in_array($row->status, [1, 2])) {
            return response()->json(array('status' => 1, 'msg' => '????????????????????????'));
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
                return response()->json(array('status' => 1, 'msg' => '???????????????'));
            }
        }
        if (!$row->save()) {
            DB::rollBack();
            return response()->json(array('status' => 1, 'msg' => '???????????????'));
        }
        DB::commit();
        return response()->json(array('status' => 0, 'msg' => '???????????????'));
    }

    /**
     * ???????????????????????????
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
     * ???????????????????????????
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

            //????????????
            $param = [];
            $param['start_date'] = $request->get('start_date');    //????????????
            $param['end_date'] = $request->get('end_date');        //????????????
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

                //??????????????????
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
     * ?????????????????????
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function putWithdrawalDelayVerify(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $row = ActivityWithdrawalDelay::where('id', $id)->first();
        if (!$row) {
            return response()->json(array('status' => -1, 'msg' => '???????????????'));
        }
        if ($row->status) {
            return response()->json(array('status' => 1, 'msg' => '????????????????????????'));
        }

        $activity = Activity::where('id', $row->activity_id)->where('status', 1)->first();
        if (empty($activity)) {
            return response()->json(array('status' => 1, 'msg' => '????????????????????????'));
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
                return response()->json(array('status' => 1, 'msg' => '???????????????'));
            }
        }
        if (!$row->save()) {
            DB::rollBack();
            return response()->json(array('status' => 1, 'msg' => '???????????????'));
        }
        DB::commit();
        return response()->json(array('status' => 0, 'msg' => '???????????????'));
    }

    /**
     * ?????????????????????
     */
    public function getPrizepool()
    {
        return view('activity.prizepool');
    }

    /**
     * ?????????????????????
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
     * ?????????????????????
     */
    public function putPrizepoolverify(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $row = PrizePool::where('id', $id)->first();
        if (!$row) {
            return response()->json(array('status' => -1, 'msg' => '???????????????'));
        }

        if ($row->frozen != 1) {
            return response()->json(array('status' => 1, 'msg' => '???????????????'));
        }

        if (in_array($row->status, [1, 2])) {
            return response()->json(array('status' => 1, 'msg' => '????????????????????????'));
        }

        $status = (int)$request->get('status', 0);
        if (!in_array($status, [1, 2])) {
            return response()->json(array('status' => -1, 'msg' => '???????????????'));
        }

        $row->status = $status;
        $row->verified_admin_user_id = auth()->id();
        $row->verified_at = (string)Carbon::now();

        DB::beginTransaction();
        if ($status == 1) {
            $api_prizepool = new APIPrizepool();
            if (!$api_prizepool->verify($row)) {
                DB::rollBack();
                return response()->json(array('status' => 1, 'msg' => '??????????????????'));
            }
        }
        if (!$row->save()) {
            DB::rollBack();
            return response()->json(array('status' => 1, 'msg' => '???????????????'));
        }
        DB::commit();
        return response()->json(array('status' => 0, 'msg' => '???????????????'));
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
            return redirect()->back()->withErrors("?????????????????????????????????");
        }
        if ($activity->draw_method != 1) {
            return redirect()->back()->withErrors("???????????????????????????");
        }
        return view('activity.sendaward', ['activity' => $activity]);
    }

    /*
     * ????????????
     */
    public function postSendaward(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $money = (float)$request->get('money', 0);
        $award_user = $request->get('award_user', '');
        $desc = $request->get('description', '');
        if (empty($award_user)) {
            return redirect()->back()->withErrors("????????????????????????");
        }
        if (empty($money) || !is_numeric($money)) {
            return redirect()->back()->withErrors("???????????????");
        }
        if (empty($desc)) {
            return redirect()->back()->withErrors("???????????????");
        }
        $activity = Activity::find($id);
        if (empty($activity)) {
            return redirect()->back()->withErrors("?????????????????????????????????");
        }
        if ($activity->draw_method != 1) {
            return redirect()->back()->withErrors("???????????????????????????");
        }
        $award_user = explode(',', $award_user);
        $users = User::select(['id', 'username'])->whereIn('username', $award_user)->get();
        $success = [];
        //????????????
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
                "ordertypetext" => "????????????"
            ];

            $second_verify->data = json_encode($data);
            if (!$second_verify->save()) {
                continue;
            }
            $success[] = $user->username;
        }
        if (empty($success)) {
            return redirect()->back()->withErrors("????????????,???????????????");
        }
        return redirect()->back()->withSuccess("??????" . implode(',', $success) . "??????????????????????????????????????????");
    }

    /**
     * ???????????????
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
                    return redirect()->back()->withErrors("{$period} ????????????");
                }
                if ($period_info->code_status !== 0) {
                    return redirect()->back()->withErrors("???????????????????????????");
                }
                $period_info->code_status = 2;
                $period_info->save();
                return redirect()->back()->withSuccess("{$period_info->period}????????????");
                break;
            case 'period_draw':
                $period = $request->get('period', '');
                $jackpot = new \Service\API\Activity\Jackpot();
                if ($jackpot->draw($period)) {
                    return redirect()->back()->withSuccess("{$period}?????????????????????");
                } else {
                    return redirect()->back()->withErrors("{$jackpot->ret_msg}");
                }
                break;
            case 'operation_prize':
                $period = $request->get('period', '');
                $period_info = \Service\Models\JackpotPeriod::where('period', $period)->first();
                if (empty($period_info)) {
                    return redirect()->back()->withErrors("{$period} ????????????");
                }
                $now_date_time = Carbon::now()->toDateTimeString();
                if ($period_info->end_at <= $now_date_time) {
                    return redirect()->back()->withErrors("{$period} ?????? {$period_info->end_at} ?????????????????????????????????");
                }
                return view('activity.jackpot_operation_prize', ['period_info' => $period_info]);
                break;
            case 'user_code':
                $period = $request->get('period', '');
                $period_info = \Service\Models\JackpotPeriod::where('period', $period)->first();
                if (empty($period_info)) {
                    return redirect()->back()->withErrors("{$period} ????????????");
                }
                return view('activity.jackpot_user_code', ['period_info' => $period_info]);
                break;
            case 'period_postpone': //??????
                $period = $request->get('period', '');
                $period_info = \Service\Models\JackpotPeriod::where('period', $period)->first();
                if (empty($period_info)) {
                    return redirect()->back()->withErrors("{$period} ????????????");
                }
                $next_sunday_date = Carbon::parse($period_info->end_at)->addDay(1)->endOfWeek()->toDateString();
                $check_period = \Service\Models\JackpotPeriod::where('start_at', '<', $next_sunday_date)
                    ->where('end_at', '>', $next_sunday_date)
                    ->first();
                if ($check_period) {
                    return redirect()->back()->withErrors("??????????????????????????????{$check_period->period}?????????{$check_period->start_at}?????????{$check_period->end_at}");
                }
                $period_info->end_at = $next_sunday_date . ' 23:59:59';
                if ($period_info->save()) {
                    return redirect()->back()->withSuccess("{$period_info->period} ??????????????????????????????????????????{$period_info->end_at}");
                } else {
                    return redirect()->back()->withErrors("{$period_info->period} ??????????????????");
                }
                break;
            case 'period_delete': //??????
                $period = $request->get('period', '');
                $period_info = \Service\Models\JackpotPeriod::where('period', $period)->first();
                if (empty($period_info)) {
                    return redirect()->back()->withErrors("{$period} ????????????");
                }
                $now_date_time = Carbon::now()->toDateString();
                if ($period_info->end_at < $now_date_time) {
                    return redirect()->back()->withErrors("{$period} ???????????????????????????");
                }
                //????????????????????????
                $check_user_codes = \Service\Models\JackpotUserCode::where('period', $period)->first();
                if ($check_user_codes) {
                    return redirect()->back()->withErrors("{$period} ???????????????????????????????????????????????????");
                }
                //??????????????????
                set_time_limit(200);
                \Service\Models\JackpotPeriod::where('period', $period)->delete();
                //???????????????
                \Service\Models\JackpotPeriodCodeData::where('period', $period)->delete();
                return redirect()->back()->withSuccess("{$period_info->period} ????????????");
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
            case 'operation_prize': //????????????
                $period = $request->get('period', '');
                $period_info = \Service\Models\JackpotPeriod::where('period', $period)->first();
                if (empty($period_info)) {
                    return redirect()->back()->withErrors("{$period} ????????????");
                }
                $operation_prize = $request->get('operation_prize', 0);
                if (!is_numeric($operation_prize)) {
                    return redirect()->back()->withErrors("?????????????????????");
                }
                $period_info->operation_prize = $operation_prize;
                $period_info->save();
                return redirect('/activity/jackpot\/')->withSuccess('???' . $period_info->period . '???????????????????????????');
            case 'user_code': //??????????????????
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
                            if ($search['value'] == '??????') {
                                $query->where('jackpot_user_code.prize_level', '>', 0);
                            } elseif ($search['value'] == '?????????') {
                                $query->where('jackpot_user_code.prize_level', '=', 1);
                            } elseif ($search['value'] == '?????????') {
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
                            if ($search['value'] == '??????') {
                                $query->where('jackpot_user_code.prize_level', '>', 0);
                            } elseif ($search['value'] == '?????????') {
                                $query->where('jackpot_user_code.prize_level', '=', 1);
                            } elseif ($search['value'] == '?????????') {
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

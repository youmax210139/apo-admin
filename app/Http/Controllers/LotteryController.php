<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LotteryCreateRequest;
use App\Http\Requests\LotteryUpdateRequest;
use App\Http\Requests\IssueCreateRequest;
use App\Http\Requests\IssueDeleteRequest;
use Illuminate\Support\Facades\Redis;
use Service\API\SendPrize\Draw\Draw;
use Service\Models\Lottery;
use Service\Models\Issue;
use Service\Models\LotteryCategory;
use Service\Models\LotteryMethod;
use Service\Models\LotteryMethodCategory;
use Service\API\Issue\Generator;
use Service\API\Issue\Traits\CreateConfirmXglhc;
use Service\Models\LotteryMethodPrizeLevel;
use Cache;

class LotteryController extends Controller
{
    use CreateConfirmXglhc;

    protected $fields = [
        'lottery_category_id' => 1,
        'lottery_method_category_id' => '',
        'ident' => '',
        'name' => '',
        'official_url' => '',
        'week_cycle' => 0,
        'number_rule' => array('len' => ''),
        'issue_rule' => '',
        'min_profit' => 0.02,
        'min_spread' => 0.01,
        'issue_set' => array(),
        'closed_time_start' => '',
        'closed_time_end' => '',
        'special' => 0,
        'special_config' => '{
        "hand_coding": 0,
    "sleep_seconds": 5,
    "max_time": 5,
    "times": 5,
    "probability": 10
}',
        'deny_user_group' => [],
        'cron' => '* * * * *'
    ];

    public function getIndex(Request $request)
    {
        $data = [];
        $data['method_category_rows'] = \Service\Models\LotteryMethodCategory::where('parent_id', '=', 0)
            ->orderBy('id', 'asc')
            ->get();
        $data['mcid'] = (int)$request->get('mcid', 0);
        $data['special'] = (int)$request->get('special', 0);
        $data['refresh_at'] = Cache::store('redis')->get('cronLotteryLastRefreshAt', '');
        return view('lottery.index', $data);
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
            $search = $request->get('search');
            $method_catid = (int)$request->get('mcid', 0);
            $special = (int)$request->get('special', 0);
            $where_array = [];
            if ($method_catid > 0) {
                $where_array[] = ['lottery.lottery_method_category_id', '=', $method_catid];
            }
            if ($special > 0) {
                $where_array[] = ['lottery.special', '>', 0];
            }
            $data['recordsTotal'] = Lottery::where($where_array)->count();
            if (strlen($search['value']) > 0) {
                $data['recordsFiltered'] = Lottery::where(function ($query) use ($search) {
                    $query->where('name', 'LIKE', '%' . $search['value'] . '%');
                })->count();

                $data['data'] = Lottery::leftJoin(
                    'lottery_category',
                    'lottery.lottery_category_id',
                    '=',
                    'lottery_category.id'
                )
                    ->where(function ($query) use ($search) {
                        $query->where('lottery.name', 'LIKE', '%' . $search['value'] . '%');
                    })
                    ->select(
                        'lottery.id',
                        'lottery.name',
                        'lottery.special',
                        'lottery.ident',
                        'lottery_category.name as category_name',
                        'lottery.status',
                        'lottery.introduce_status',
                        'lottery.deny_user_group'
                    )
                    ->skip($start)->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->get();
            } else {
                $data['recordsFiltered'] = $data['recordsTotal'];
                $data['data'] = Lottery::leftJoin(
                    'lottery_category',
                    'lottery.lottery_category_id',
                    '=',
                    'lottery_category.id'
                )
                    ->select(
                        'lottery.id',
                        'lottery.name',
                        'lottery.special',
                        'lottery.ident',
                        'lottery_category.name as category_name',
                        'lottery.status',
                        'lottery.introduce_status',
                        'lottery.deny_user_group'
                    )
                    ->where($where_array)
                    ->skip($start)->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->get();
            }
            foreach ($data['data'] as &$item) {
                //????????????
                $deny_user_group = json_decode($item->deny_user_group, true);
                if (in_array(1, $deny_user_group) && in_array(3, $deny_user_group)) { //?????????????????????
                    $item->maintenance = 1;
                } else {
                    $item->maintenance = 0;
                }
                unset($item->deny_user_group);
            }
            return response()->json($data);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCreate()
    {
        $data = [];
        $data['hours'] = range(0, 23);
        $times = range(0, 59);
        for ($i = 0; $i < 10; $i++) {
            $data['hours'][$i] = '0' . $data['hours'][$i];
            $times[$i] = '0' . $times[$i];
        }
        $data['times'] = $times;
        foreach ($this->fields as $field => $default) {
            $data[$field] = old($field, $default);
            if ($field == 'special_config') {
                $data[$field] = json_decode($default);
            }
            if ($field == 'cron') {
                $data[$field] = explode(' ', $default);
            }
        }
        $data['lottery_category'] = LotteryCategory::all();
        $data['lottery_method_category'] = LotteryMethodCategory::where('parent_id', 0)->get();
        $data['china_num'] = array('???', '???', '???', '???', '???', '???', '???', '???', '???', '???');

        return view('lottery.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LotteryCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(LotteryCreateRequest $request)
    {
        $lottery = new Lottery();
        $input = $request->all();

        foreach (array_keys($this->fields) as $field) {
            if (in_array(
                $field,
                array('number_rule', 'issue_rule', 'issue_set', 'deny_user_group', 'special_config')
            )) {
                if (isset($input[$field])) {
                    $lottery[$field] = json_encode($input[$field]);
                }
            } elseif (in_array($field, array('week_cycle'))) {
                $lottery[$field] = array_sum($input[$field]);
            } else {
                $lottery[$field] = isset($input[$field]) ? $input[$field] : $this->fields[$field];
            }
        }
        //????????????
        $cron_minute = isset($input['cron_minute']) ? trim($input['cron_minute']) : '*';
        $cron_hour = isset($input['cron_hour']) ? trim($input['cron_hour']) : '*';
        if (!preg_match('`^[0-9,\-*\/]+$`', $cron_minute)) {
            return redirect()->back()->withErrors('??????????????????????????????????????? ?????? , - * /??????');
        }
        if (!preg_match('`^[0-9,\-*\/]+$`', $cron_hour)) {
            return redirect()->back()->withErrors('??????????????????????????????????????? ?????? , - * /??????');
        }
        $lottery['cron'] = "{$cron_minute} {$cron_hour} * * *";

        $lottery->save();
        return redirect('/lottery\/')->withSuccess('????????????');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param LotteryCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $row = Lottery::find($id);
        if (!$row) {
            return redirect('/lottery\/')->withErrors("??????????????????");
        }
        $data = ['id' => $id];
        foreach (array_keys($this->fields) as $field) {
            if (in_array($field, array('number_rule', 'issue_rule', 'issue_set', 'deny_user_group'))) {
                $data[$field] = json_decode($row[$field], true);
            } elseif (in_array($field, array('special_config'))) {
                $data[$field] = json_decode($row[$field]);
                if (!empty($data[$field])) {
                    $data[$field]->hand_coding = isset($data[$field]->hand_coding) ? intval($data[$field]->hand_coding) : 0;
                }
            } elseif (in_array($field, array('week_cycle'))) {
                //$data[$field] = array_sum($row[$field]);
                $data[$field] = $row[$field];
            } elseif ($field == 'cron') {
                $data['cron'] = explode(' ', $row[$field]);
            } else {
                $data[$field] = old($field, $row->$field);
            }
        }

        $data['hours'] = range(0, 23);
        $times = range(0, 59);
        for ($i = 0; $i < 10; $i++) {
            $data['hours'][$i] = str_pad($data['hours'][$i], 2, '0', STR_PAD_LEFT);
            $times[$i] = str_pad($times[$i], 2, '0', STR_PAD_LEFT);
        }
        $data['times'] = $times;

        $data['lottery_category'] = LotteryCategory::all();
        $data['lottery_method_category'] = LotteryMethodCategory::all();
        $data['id'] = (int)$id;

        $data['china_num'] = array('???', '???', '???', '???', '???', '???', '???', '???', '???', '???');

        return view('lottery.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LotteryUpdateRequest|Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function putEdit(LotteryUpdateRequest $request)
    {
        $row = Lottery::find((int)$request->get('id', 0));
        if (!$row) {
            return redirect('/lottery\/')->withErrors("??????????????????");
        }

        $input = $request->all();

        foreach (array_keys($this->fields) as $field) {
            if (in_array(
                $field,
                array('number_rule', 'issue_rule', 'issue_set', 'deny_user_group', 'special_config')
            )) {
                if (isset($input[$field])) {
                    $row->$field = json_encode($input[$field]);
                } else {
                    $row->$field = '[]';
                }
            } elseif (in_array($field, array('week_cycle'))) {
                $row->$field = array_sum($input[$field]);
            } else {
                $row->$field = isset($input[$field]) ? $input[$field] : $this->fields[$field];
            }
        }
        //????????????
        $cron_minute = isset($input['cron_minute']) ? trim($input['cron_minute']) : '*';
        $cron_hour = isset($input['cron_hour']) ? trim($input['cron_hour']) : '*';
        if (!preg_match('`^[0-9,\-*\/]+$`', $cron_minute)) {
            return redirect()->back()->withErrors('??????????????????????????????????????? ?????? , - * /??????');
        }
        if (!preg_match('`^[0-9,\-*\/]+$`', $cron_hour)) {
            return redirect()->back()->withErrors('??????????????????????????????????????? ?????? , - * /??????');
        }
        $row->cron = "{$cron_minute} {$cron_hour} * * *";
        unset($row->lottery_method_category_id);//????????????????????????

        $row->save();
        return redirect()->back()->withSuccess($row->name . ' ????????????!');
    }

    /**
     * ??????????????????
     *
     * @param Request $request
     * @return unknown|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function putSetStatus(Request $request)
    {
        $id = (int)$request->get('id');
        $status = (int)$request->get('status');

        $lottery = Lottery::find($id);

        //??????????????????
        $action = $request->get('action', '');
        if ($action == 'maintenance') {
            $maintenance = (int)$request->get('maintenance', 0);
            $deny_user_group = json_decode($lottery->deny_user_group);
            if ($maintenance == 1) {
                array_push($deny_user_group, 1, 3); //?????????????????????
                $deny_user_group = array_unique($deny_user_group);
                sort($deny_user_group);
            } else {
                foreach ($deny_user_group as $k => $d_gid) {
                    if (in_array($d_gid, [1, 3])) {
                        unset($deny_user_group[$k]);
                    }
                }
                sort($deny_user_group);
            }
            $lottery->deny_user_group = json_encode($deny_user_group);
            $lottery->save();
            $maintenance_tips = $maintenance ? '????????????' : '????????????';
            return redirect()->back()->withSuccess($lottery->name . " {$maintenance_tips} ??????");
        }

        $lottery->status = $status;

        if ($lottery->save()) {
            $status_type = ($status == 1) ? '??????' : '??????';
            return redirect('/lottery/')->withSuccess("???{$lottery->name}???????????????{$status_type}???");
        } else {
            return redirect('/lottery/')->withErrors("????????????");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteIndex(Request $request)
    {
        if (app()->environment() == 'production') {
            return $this->disabled();
        }

        $row = Lottery::find((int)$request->get('id', 0));

        if ($row) {
            $row->delete();

            return redirect()->back()->withSuccess("????????????");
        }

        return redirect()->back()->withErrors("????????????");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getIssue(Request $request)
    {
        $data['lottery_id'] = (int)$request->get('lottery_id');
        $data['lottery'] = Lottery::find($data['lottery_id']);
        if (empty($data['lottery'])) {
            return redirect('/lottery/')->withSuccess("????????????????????????");
        }
        $data['lottery']->special_config = json_decode($data['lottery']->special_config);
        $data['lottery']->special_config->hand_coding = isset($data['lottery']->special_config->hand_coding) ? $data['lottery']->special_config->hand_coding : 0;
        return view('lottery.issue', $data);
    }

    public function postIssue(Request $request)
    {
        if ($request->ajax()) {
            $data = array();

            $data['draw'] = $request->get('draw');
            $lottery_id = (int)$request->get('lottery_id');
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');
            $search = $request->get('search');
            $param['issue'] = $request->get('issue', '');
            $param['sale_start'] = $request->get('sale_start', '');
            $param['sale_end'] = $request->get('sale_end', '');
            $param['code_status'] = $request->get('code_status', 0);
            $where = function ($query) use ($param) {
                if (!empty($param['issue'])) {
                    $query->where('issue', $param['issue']);
                }
                if (strtotime($param['sale_start'])) {
                    $query->where('sale_start', '>=', $param['sale_start']);
                }
                if (strtotime($param['sale_end'])) {
                    $query->where('sale_end', '<=', $param['sale_end']);
                }
                if ($param['code_status'] !== 'all' && is_numeric($param['code_status'])) {
                    $query->where('code_status', $param['code_status']);
                }
            };
            $data['recordsTotal'] = $data['recordsFiltered'] = Issue::where("lottery_id", '=', $lottery_id)
                ->where($where)->count();

            $data['recordsFiltered'] = $data['recordsTotal'];
            $data['data'] = Issue::select('issue.*', 'lottery.name as lottery_name')
                ->leftJoin('lottery', 'issue.lottery_id', '=', 'lottery.id')
                ->where("lottery_id", '=', $lottery_id)
                ->where($where)
                ->skip($start)->take($length)
                ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                ->get();

            foreach ($data['data'] as &$issue) {
                $issue->allow_hand_code = strtotime($issue->sale_start) > time() ? true : false;
            }
            return response()->json($data);
        }
    }

    /**
     * ????????????-?????????
     *
     * @return \Illuminate\Http\Response
     */
    public function getIssueCreate(Request $request)
    {
        $data = [];
        $lottery_id = (int)$request->input('lottery_id', 0);
        $data['lottery'] = Lottery::find($lottery_id);
        if (!$data['lottery']) {
            return redirect('/lottery/')->withErrors("?????????????????????");
        }
        $data['lottery']->issue_set = json_decode($data['lottery']->issue_set, true);
        $data['lottery']->issue_rule = json_decode($data['lottery']->issue_rule, true);
        $data['need_first'] = strpos($data['lottery']->issue_rule['rule'], 'd') === false;

        return view('lottery.issuecreate', $data);
    }

    /**
     * ?????????????????????
     * @param IssueCreateRequest $request
     * @return type
     */
    public function postIssueCreate(IssueCreateRequest $request)
    {
        if ($request->input("step") == "confirm") {
            return $this->issueCreateConfirm($request);
        } else {
            $lottery_id = (int)$request->input('lottery_id', 0);
            $first_date = $request->input('first_date', '');
            $start_date = strtotime($request->input('start_date'));
            $end_date = strtotime($request->input('end_date'));
            $generator = new Generator();
            if ($end_date < $start_date) {
                return redirect()->back()->withErrors("???????????????????????????????????????", 1);
            }
            //???????????????
            if ($request->input('issuefrom') == 'collect') {
                $issues = array();
                foreach ($request->input('collect_issue') as $issue) {
                    $issues[] = array(
                        'issue' => $issue,
                        'belong_date' => trim($request->input('collect_belongdate')[$issue]),
                        'sale_start' => trim($request->input('collect_salestart')[$issue]),
                        'sale_end' => trim($request->input('collect_saleend')[$issue]),
                        'cancel_deadline' => trim($request->input('collect_canceldeadline')[$issue]),
                        'earliest_write_time' => trim($request->input('collect_earliestwritetime')[$issue]),
                    );
                }
                $number = $generator->addCollectIssue($lottery_id, $start_date, $end_date, $issues);
            } else {
                //???????????????
                $number = $generator->generate($lottery_id, $first_date, $start_date, $end_date);
            }
            if (is_int($number)) {
                $lottery = Lottery::find($lottery_id);
                //?????????????????? ????????????
                Redis::del('long:' . $lottery->ident);
                return redirect('/lottery/issue?lottery_id=' . $lottery_id)->withSuccess("???????????????????????? {$number} ?????????");
            } else {
                return redirect('/lottery/issue?lottery_id=' . $lottery_id)->withErrors("?????????????????????{$number}");
            }
        }
    }

    /**
     * ??????????????????
     * @param IssueCreateRequest $request
     * @return type
     */
    private function issueCreateConfirm(IssueCreateRequest $request)
    {
        $lottery_id = (int)$request->input('lottery_id', 0);
        $first_date = $request->input('first_date');
        $start_date = strtotime($request->input('start_date'));
        $end_date = strtotime($request->input('end_date'));
        $date1 = date('Y-m-d', $start_date);  // ????????????????????????????????? ????????????
        $date2 = date('Y-m-d', $end_date);    // ????????????????????????????????? ????????????
        if ($end_date < $start_date) {
            return redirect()->back()->withErrors("????????????????????????????????????");
        }
        $lottery = Lottery::find($lottery_id);
        if (!$lottery) {
            return redirect()->back()->withErrors("?????????????????????");
        }

        //??????????????????????????????????????????
        $intersect_dates = array(
            'startday' => '0',
            'endday' => '0',
            'intersect_startday' => '0',
            'intersect_endday' => '0'
        );
        $issue_rows = Issue::select(['belong_date', DB::raw('count(*) AS count')])
            ->where('lottery_id', $lottery_id)
            ->groupBy('belong_date')
            ->orderBy('belong_date', 'ASC')
            ->get();
        $day_issues = [];
        foreach ($issue_rows as $issue) {
            $day_issues[$issue->belong_date] = $issue->count;
        }
        if ($day_issues) {
            $tmp = array_keys($day_issues);
            $intersect_dates = array(
                'startday' => reset($tmp),
                'endday' => end($tmp),
                'intersect_startday' => '0',
                'intersect_endday' => '0'
            );
            if ($date1 <= $intersect_dates['endday']) {
                $intersect_dates['intersect_startday'] = $date1 < $intersect_dates['startday'] ? $intersect_dates['startday'] : $date1;
                $intersect_dates['intersect_endday'] = $date2 >= $intersect_dates['endday'] ? $intersect_dates['endday'] : $date2;
            }
        }

        foreach ($day_issues as $k => $v) {
            if ($k == $date1) {
                $intersect_dates['intersectday'] = $k;
            }
        }

        //????????????????????????
        $data = [];
        $method_name = strtolower($lottery->ident);
        if (method_exists($this, $method_name)) {
            $result = $this->$method_name($request);
            if ($result['result']) {
                foreach ($result['data'] as $key => $val) {
                    $data[$key] = $val;
                }
            } else {
                return redirect()->back()->withErrors($result['msg']);
            }
        }

        $data["intersect_dates"] = $intersect_dates;
        $data["first_date"] = $first_date;
        $data["date1"] = $date1;
        $data["date2"] = $date2;
        $data['step'] = $request->input('step');
        $data['lottery'] = $lottery;
        $data['lottery']->issue_set = json_decode($data['lottery']->issue_set, true);
        $data['lottery']->issue_rule = json_decode($data['lottery']->issue_rule, true);
        $data['need_first'] = strpos($data['lottery']->issue_rule['rule'], 'd') === false;

        return view('lottery.issuecreate', $data);
    }

    /**
     * ????????????
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteIssueDelete(IssueDeleteRequest $request)
    {
        $delete_by = $request->get('delete_by', 'select');
        $result = false;
        $msg = '';
        if ($delete_by == 'select') {
            $ids = array_filter(explode(",", $request->input('select_ids', '')));
            if ($ids) {
                $result = Issue::whereIn('id', $ids)->delete();
            } else {
                $msg = "??????????????????????????????";
            }
        } else {
            $lottery_id = $request->input('lottery_id', 0);
            $start_time = $request->input('start_time', '');
            $end_time = $request->input('end_time', '');
            if ($start_time && $end_time) {
                $result = Issue::where('lottery_id', $lottery_id)->where(
                    'sale_start',
                    '>=',
                    $start_time
                )->where('sale_start', '<=', $end_time)->delete();
            } else {
                $msg = "????????????????????????????????????";
            }
        }
        if ($result) {
            return redirect()->back()->withSuccess("????????????,????????? {$result} ???");
        } else {
            return redirect()->back()->withErrors("???????????????????????????????????????" . ($msg ? ',' . $msg : ''));
        }
    }

    public function getBlockmethod(Request $request)
    {
        $lottery_id = $request->get('id', 0);
        $lottery = Lottery::select(['lottery.*', 'lottery_method_category.ident as type'])
            ->leftJoin('lottery_method_category', 'lottery_method_category.id', 'lottery.lottery_method_category_id')
            ->where('lottery.id', $lottery_id)
            ->first();
        if (!$lottery) {
            return redirect()->back()->withErrors("??????????????????");
        }
        $lottery->deny_method_ident = json_decode($lottery->deny_method_ident);
        $lottery_methods = LotteryMethod::select([
            'lottery_method.id',
            'lottery_method.parent_id',
            'lottery_method.name',
            'lottery_method.ident',
            'lottery_method.layout',
            'lottery_method.prize_level',
            'lottery_method.prize_level_name',
            'lottery_method.modes',
            'lottery_method_category.ident as type'
        ])->leftJoin(
            'lottery_method_category',
            'lottery_method_category.id',
            'lottery_method.lottery_method_category_id'
        )
            ->where('lottery_method_category.ident', $lottery->type . '_bz')
            ->orWhere('lottery_method_category.ident', $lottery->type . '_pk')
            ->orderBy('lottery_method.id', 'asc')
            ->get();

        $methods = array();

        $level_1 = [];
        foreach ($lottery_methods as $method) {
            if ($method->parent_id == 0) {
                $id = $method->id;

                $level_1[$id] = $method;
                $methods[$id]['id'] = $id;
                $methods[$id]['ident'] = $method->ident;
                $methods[$id]['name'] = $method->name;
                $methods[$id]['child'] = [];
            }
        }

        $level_2 = [];
        foreach ($lottery_methods as $method) {
            if (isset($level_1[$method->parent_id])) {
                $id = $method->id;
                $parent_id = $method->parent_id;
                $level_2[$id] = $method;
                $methods[$parent_id]['child'][$id]['id'] = $id;
                $methods[$parent_id]['child'][$id]['ident'] = $method->ident;
                $methods[$parent_id]['child'][$id]['name'] = $method->name;
                $methods[$parent_id]['child'][$id]['child'] = [];
            }
        }

        foreach ($lottery_methods as $method) {
            if (isset($level_2[$method->parent_id])) {
                $parent_id = $method->parent_id;
                $top_id = $level_2[$parent_id]->parent_id;
                $id = $method->id;

                $methods[$top_id]['child'][$parent_id]['child'][$id]['id'] = $id;
                $methods[$top_id]['child'][$parent_id]['child'][$id]['name'] = $method->name;
                $methods[$top_id]['child'][$parent_id]['child'][$id]['ident'] = $method->ident;
            }
        }

        unset($lottery_methods);

        foreach ($methods as &$value) {
            if ($value['child']) {
                $value['child'] = array_values($value['child']);
            }
            foreach ($value['child'] as &$v) {
                if ($v['child']) {
                    $v['child'] = array_values($v['child']);
                }
            }
        }

        $methods = array_values($methods);
        return view('lottery.block_method', array('lottery' => $lottery, 'lottery_methods' => $methods));
    }

    public function postBlockmethod(Request $request)
    {
        $lottery_id = $request->get('id', 0);
        $deny_methods = $request->get('deny_methods', array());
        $lottery = Lottery::where('lottery.id', $lottery_id)->first();
        if (!$lottery) {
            return redirect()->back()->withErrors("??????????????????");
        }
        $lottery->deny_method_ident = json_encode($deny_methods);
        $lottery->save();
        return redirect()->back()->withSuccess("????????????");
    }

    public function postEditcode(Request $request)
    {
        $issue_id = (int)$request->get('id', 0);
        $code = $request->get('code', '');
        if (empty($code)) {
            return response()->json(array('status' => -1, 'msg' => '?????????????????????'));
        }
        DB::beginTransaction();
        $issue = Issue::where('id', $issue_id)->lockForUpdate()->first();
        if (!$issue) {
            DB::rollBack();
            return response()->json(array('status' => 1, 'msg' => '??????????????????????????????'));
        }
        if (strtotime($issue->sale_end) <= time() || $issue->code_status <> 0) {
            DB::rollBack();
            return response()->json(array('status' => 1, 'msg' => '?????????????????????????????????????????????'));
        }
        $lottery = Lottery::select([
            'lottery.*',
            'lottery_category.ident as lottery_category_ident',
            'lottery_method_category.ident as lottery_method_category_ident',
        ])
            ->leftJoin('lottery_category', 'lottery_category.id', 'lottery.lottery_category_id')
            ->leftJoin('lottery_method_category', 'lottery_method_category.id', 'lottery.lottery_method_category_id')
            ->where('lottery.id', $issue->lottery_id)
            ->first();
        if (empty($lottery)) {
            DB::rollBack();
            return response()->json(array('status' => 1, 'msg' => '??????????????????????????????'));
        }
        if ($lottery->special !== 1) {
            DB::rollBack();
            return response()->json(array('status' => 1, 'msg' => '?????????????????????????????????'));
        }
        $lottery->special_config = json_decode($lottery->special_config, true);
        if (!isset($lottery->special_config['hand_coding']) || $lottery->special_config['hand_coding'] != 1) {
            DB::rollBack();
            return response()->json(array('status' => 1, 'msg' => '?????????????????????????????????'));
        }
        $check_code = \Service\API\Drawsource\Change::checkCode($lottery->lottery_method_category_ident, $code);
        if ($check_code == false) {
            DB::rollBack();
            return response()->json(array('status' => 1, 'msg' => '??????????????????1'));
        }
        $lottery_code_rules = json_decode($lottery->number_rule, true);
        if (!Draw::verifyIssueCode($code, $lottery_code_rules, $lottery->lottery_category_ident)) {
            DB::rollBack();
            return response()->json(array('status' => 1, 'msg' => '??????????????????2'));
        }
        $issue->code = $code;
        $issue->save();
        DB::commit();
        return response()->json(array('status' => 0, 'msg' => '??????????????????'));
    }

    /**
     * ????????????????????????????????????0?????????0?????????0?????????0?????????0?????????-1???
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postResetissuestatus(Request $request)
    {
        $id = $request->get('id', 0);
        $issue = Issue::find($id);
        if (!$issue) {
            return response()->json(array('status' => 1, 'msg' => '??????????????????????????????'));
        }
        if (strtotime($issue->sale_end) > time()) {
            return response()->json(array('status' => 1, 'msg' => '???????????????????????????????????????'));
        }
        try {
            $issue->check_bonus_status = 0;
            $issue->bonus_status = 0;
            $issue->deduct_status = 0;
            $issue->rebate_status = 0;
            $issue->task_to_project_status = 0;
            $issue->report_status = -1;
            $issue->save();
        } catch (\Exception $exception) {
            return response()->json(array('status' => 1, 'msg' => $exception->getMessage()));
        }
        return response()->json(array('status' => 0, 'msg' => '???????????????????????????'));
    }

    /*
    * ??????????????????
    * @param Request $request
    */
    public function getPrizelevel(Request $request)
    {
        $lottery_id = (int)$request->get('id', 0);
        $lottery = Lottery::find($lottery_id);
        if (!$lottery) {
            return redirect()->back()->withErrors("??????????????????");
        }
        $data = DB::select("select
       m.id,
	(m1.\"name\" || ' - ' || m.\"name\") as name,
	m.prize_level,
	m.prize_level_name,
	lmp.prize_level as prize_level_fixed
from
	lottery_method m
	left join
	lottery_method m1 on m1.id=m.parent_id
	left join lottery_method_prize_level lmp on lmp.lottery_method_id=m.id and lmp.lottery_id=:lottery_id
where
	m.prize_level::text != '[]'
	and m.lottery_method_category_id>:cate_id
	and m.lottery_method_category_id<:cate_id_max ORDER BY m.id ASC", [
            'lottery_id' => $lottery_id,
            'cate_id' => $lottery->lottery_method_category_id,
            'cate_id_max' => $lottery->lottery_method_category_id + 3
        ]);
        return view('lottery.prize_level', ['lists' => $data, 'lottery' => $lottery]);
    }

    public function postPrizelevel(Request $request)
    {
        $lottery_id = (int)$request->get('lottery_id', 0);
        $method_id = (int)$request->get('method_id', 0);
        $prize_level = $request->get('prize_level');
        if (!is_array($prize_level)) {
            return redirect()->back()->withErrors("????????????????????????");
        }
        foreach ($prize_level as $item) {
            if (!is_numeric($item)) {
                return redirect()->back()->withErrors("????????????????????????");
            }
        }
        $method_prize_level = LotteryMethodPrizeLevel::where('lottery_id', $lottery_id)
            ->where('lottery_method_id', $method_id)
            ->first();
        if ($method_prize_level) {
            $method_prize_level->prize_level = json_encode($prize_level);
            $method_prize_level->save();
        } else {
            $method_prize_level = new LotteryMethodPrizeLevel();
            $method_prize_level->lottery_id = $lottery_id;
            $method_prize_level->lottery_method_id = $method_id;
            $method_prize_level->prize_level = json_encode($prize_level);
            $method_prize_level->save();
        }
        return redirect('lottery/prizelevel?id=' . $lottery_id)->withSuccess("???????????????");
    }

    public function postDelprizelevel(Request $request)
    {
        $lottery_id = (int)$request->get('lottery_id', 0);
        $method_id = (int)$request->get('method_id', 0);
        LotteryMethodPrizeLevel::where('lottery_id', $lottery_id)
            ->where('lottery_method_id', $method_id)
            ->delete();
        return redirect('lottery/prizelevel?id=' . $lottery_id)->withSuccess("?????????????????????");
    }

    public function getRefreshCron()
    {
        $rows = \Service\Models\Lottery::select(['ident', 'cron','id','issue_rule'])
            ->where('status', true)
            ->where('special', '<', 2)
            ->orderBy('id', 'asc')
            ->get();
        Cache::store('redis')->put('cronLotteryRows', $rows, 1440);
        Cache::store('redis')->forever('cronLotteryLastRefreshAt', auth()->user()->username . "|" . date('Y-m-d H:i:s'));
        return redirect()->back()->withSuccess("????????????????????????");
    }
}

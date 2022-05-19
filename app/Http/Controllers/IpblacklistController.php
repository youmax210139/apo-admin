<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use itbdw\Ip\IpLocation;
use Service\Models\IpFirewall as IpFirewallModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class IpblacklistController extends Controller
{
    protected $fields = [
        'ip' => '',
        'remark' => '',
    ];

    protected $ip_type = 'user'; //IP类型

    public function getIndex()
    {
        return view('ipblacklist.index');
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
            $wheres_array = [
                ['type', '=', $this->ip_type],
            ];
            $data['recordsTotal'] = IpFirewallModel::where($wheres_array)->count();
            if (strlen($search['value']) > 0) {
                if (filter_var($search['value'], FILTER_VALIDATE_IP)) {
                    $wheres_array[] = ['ip', '>>=', DB::raw("inet '{$search['value']}'")];
                } else {
                    $wheres_array[] = ['remark', 'LIKE', '%' . $search['value'] . '%'];
                }
                $data['recordsFiltered'] = IpFirewallModel::where($wheres_array)->count();

                $data['data'] = IpFirewallModel::where($wheres_array)->skip($start)->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->get();
            } else {
                $data['recordsFiltered'] = $data['recordsTotal'];
                $data['data'] = IpFirewallModel::where($wheres_array)->skip($start)->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->get();
            }
            foreach ($data['data'] as $key => $item) {
                if (false !== filter_var($item->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    $location = IpLocation::getLocation($item->ip);
                    $data['data'][$key]->ip = implode(" ", $location);
                }
            }
            return response()->json($data);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCreate(Request $request)
    {
        //批量添加IP
        if ($request->get('type') == 'many') {
            $data = [];
            return view('ipblacklist.create_many_form', $data);
        }

        $data = [];
        foreach ($this->fields as $field => $default) {
            $data[$field] = old($field, $default);
        }

        return view('ipblacklist.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RoleCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(Request $request)
    {
        //批量添加IP
        if ($request->get('type') == 'many') {
            return $this->postCreateMany($request);
        }

        $model = new IpFirewallModel();
        foreach (array_keys($this->fields) as $field) {
            $model->$field = trim($request->get($field));
        }
        $model->type = $this->ip_type;

        if (empty($model->ip) || !$this->_ipRule($model->ip)) {
            return redirect('/ipblacklist\/create')->withErrors("IP地址不正确");
        }
        $check = IpFirewallModel::where('type', $this->ip_type)
            ->where('ip', '>>=', DB::raw("inet '{$model->ip}'"))
            ->first();
        if ($check) {
            return redirect('/ipblacklist\/create')->withErrors("已存在相同的IP信息：{$check->ip} ，备注：{$check->remark} ，操作管理员：{$check->admin} ，添加时间：{$check->created_at}");
        }
        $model->admin = auth()->user()->username;

        $model->save();
        return redirect('/ipblacklist\/')->withSuccess('添加成功');
    }

    /**
     * 批量添加IP
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateMany(Request $request)
    {
        $inserts_data = [];
        $errors = [];
        $date_time = Carbon::now();
        $ips_many = trim($request->post('ips_many', ''));
        if (!empty($ips_many)) {
            $rows = explode("\n", $ips_many);
            foreach ($rows as $line) {
                $temp = array_filter(explode(',', $line), 'trim');
                $ip = isset($temp[0]) ? trim($temp[0]) : '';
                if (!$this->_ipRule($ip)) {
                    $errors[] = $line . " IP格式错误";
                } else {
                    $inserts_data[] = [
                        'type' => $this->ip_type,
                        'ip' => $ip,
                        'remark' => isset($temp[1]) ? trim($temp[1]) : '',
                        'admin' => auth()->user()->username,
                        'created_at' => $date_time,
                        'updated_at' => $date_time,
                    ];
                }
            }
        }
        //上传文件
        $file = $request->file('ips_file');
        if (!empty($file)) {
            $validator = Validator::make($request->all(), [
                'ips_file' => 'required|mimes:csv,txt'
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors("失败：上传文件不正确，不是CSV格式");
            }
            $file_contents = File::get($file);
            if (mb_detect_encoding($file_contents) != "UTF-8") {
                return redirect()->back()->withErrors("只能上传UTF-8格式文件");
            }
            if (!empty($file_contents)) {
                $rows = explode("\n", $file_contents);
                foreach ($rows as $line) {
                    $temp = array_filter(explode(',', $line), 'trim');
                    $ip = isset($temp[0]) ? trim($temp[0]) : '';
                    if (!$this->_ipRule($ip)) {
                        $errors[] = $line . " IP格式错误";
                    } else {
                        $inserts_data[] = [
                            'type' => $this->ip_type,
                            'ip' => $ip,
                            'remark' => isset($temp[1]) ? trim($temp[1]) : '',
                            'admin' => auth()->user()->username,
                            'created_at' => $date_time,
                            'updated_at' => $date_time,
                        ];
                    }
                }
            }
        }
        if ($errors) {
            return redirect()->back()->withErrors("失败：\n" . implode("\n ", $errors));
        }
        if (empty($inserts_data)) {
            return redirect()->back()->withErrors("失败：内容为空");
        }

        $sql = '';
        foreach ($inserts_data as $data) {
            $data['remark'] = str_replace('\'', '', $data['remark']);
            $sql .= "insert into ip_firewall(type,ip,remark,admin,created_at,updated_at) values('{$data['type']}','{$data['ip']}','{$data['remark']}','{$data['admin']}','{$data['created_at']}','{$data['updated_at']}') ON CONFLICT (type,ip) DO NOTHING;";
        }

        DB::unprepared($sql);

        return redirect('/ipblacklist\/')->withSuccess('批量添加IP成功');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $row = IpFirewallModel::where('id', $id)
            ->where('type', $this->ip_type)
            ->first();
        if (!$row) {
            return redirect('/ipblacklist\/')->withErrors("找不到IP记录");
        }
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $row->$field);
        }
        $data['id'] = $id;
        return view('ipblacklist.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param RoleUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function putEdit(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $row = IpFirewallModel::where('id', $id)
            ->where('type', $this->ip_type)
            ->first();
        if (!$row) {
            return redirect('/ipblacklist\/')->withErrors("找不到IP记录");
        }
        foreach (array_keys($this->fields) as $field) {
            $row->$field = trim($request->get($field));
        }

        if (empty($row->ip) || !$this->_ipRule($row->ip)) {
            return redirect('/ipblacklist\/edit?id=' . $id)->withErrors("IP地址不正确");
        }
        $check = IpFirewallModel::where('type', $this->ip_type)
            ->where('ip', '>>=', DB::raw("inet '{$row->ip}'"))
            ->where('id', '!=', $id)
            ->first();
        if ($check) {
            return redirect('/ipblacklist\/edit?id=' . $id)->withErrors("已存在相同的IP信息：{$check->ip} ，备注：{$check->remark} ，操作管理员：{$check->admin} ，添加时间：{$check->created_at}");
        }
        $row->save();
        return redirect('/ipblacklist\/')->withSuccess('修改成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteIndex(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $row = IpFirewallModel::where('id', $id)
            ->where('type', $this->ip_type)
            ->first();

        if ($row) {
            $row->delete();

            return redirect()->back()->withSuccess("删除成功");
        }

        return redirect()->back()->withErrors("删除失败");
    }

    /**
     * IP规则，支持192.168.1/24
     * @param $ip
     * @return bool
     */
    protected function _ipRule($ip)
    {
        if (!empty($ip) && (preg_match('`^[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d\/]{1,5}$`', $ip) || preg_match('/^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?(\/\d+)?\s*$/', $ip))) {
            return true;
        } else {
            return false;
        }
    }
}

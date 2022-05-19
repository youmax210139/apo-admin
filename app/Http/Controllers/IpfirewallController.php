<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use itbdw\Ip\IpLocation;
use Service\Models\IpFirewall as IpFirewallModel;
use Illuminate\Support\Facades\DB;

class IpfirewallController extends Controller
{
    protected $fields = [
        'ip' => '',
        'remark' => '',
    ];

    protected $ip_type = 'admin'; //IP类型

    public function getIndex()
    {
        return view('ipfirewall.index');
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
                if ($this->_checkIP($search['value'])) {
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
    public function getCreate()
    {
        $data = [];
        foreach ($this->fields as $field => $default) {
            $data[$field] = old($field, $default);
        }

        return view('ipfirewall.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RoleCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(Request $request)
    {
        $model = new IpFirewallModel();
        foreach (array_keys($this->fields) as $field) {
            $model->$field = trim($request->get($field));
        }
        $model->type = $this->ip_type;

        if (empty($model->ip) || !$this->_checkIP($model->ip)) {
            return redirect('/ipfirewall\/create')->withErrors("IP地址不正确");
        }

        $taboo = ['台湾', '臺灣', '台灣', 'TW', 'tw', 'taipei', '台北', '臺北'];
        foreach ($taboo as $val) {
            if (strpos($model->remark, $val) !== false) {
                return redirect('/ipfirewall\/create')->withErrors("备注包含敏感字词");
            }
        }

        $check = IpFirewallModel::where('type', 'admin_black')
            ->where('ip', '>>=', DB::raw("inet '{$model->ip}'"))
            ->first();
        if ($check) {
            return redirect('/ipfirewall\/create')->withErrors("不允许添加限制地区IP");
        }

        $check = IpFirewallModel::where('type', $this->ip_type)
            ->where('ip', '>>=', DB::raw("inet '{$model->ip}'"))
            ->first();
        if ($check) {
            return redirect('/ipfirewall\/create')->withErrors("已存在相同的IP信息：{$check->ip} ，备注：{$check->remark} ，操作管理员：{$check->admin} ，添加时间：{$check->created_at}");
        }
        $model->admin = auth()->user()->username;

        $model->save();
        return redirect('/ipfirewall\/')->withSuccess('添加成功');
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
            return redirect('/ipfirewall\/')->withErrors("找不到IP记录");
        }
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $row->$field);
        }
        $data['id'] = $id;
        return view('ipfirewall.edit', $data);
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
            return redirect('/ipfirewall\/')->withErrors("找不到IP记录");
        }
        foreach (array_keys($this->fields) as $field) {
            $row->$field = trim($request->get($field));
        }

        if (empty($row->ip) || !$this->_checkIP($row->ip)) {
            return redirect('/ipfirewall\/edit?id=' . $id)->withErrors("IP地址不正确");
        }
        $check = IpFirewallModel::where('type', $this->ip_type)
            ->where('ip', '>>=', DB::raw("inet '{$row->ip}'"))
            ->where('id', '!=', $id)
            ->first();
        if ($check) {
            return redirect('/ipfirewall\/edit?id=' . $id)->withErrors("已存在相同的IP信息：{$check->ip} ，备注：{$check->remark} ，操作管理员：{$check->admin} ，添加时间：{$check->created_at}");
        }
        $row->save();
        return redirect('/ipfirewall\/')->withSuccess('修改成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteIndex(Request $request)
    {
        $id = $request->get('id', 0);
        if (empty($id)) {
            return redirect()->back()->withErrors("请选择删除的IP记录");
        }
        $ids = explode(',', $id);
        $result = IpFirewallModel::whereIn('id', $ids)
            ->where('type', $this->ip_type)
            ->delete();

        if ($result) {
            return redirect()->back()->withSuccess("删除成功");
        } else {
            return redirect()->back()->withErrors("删除失败");
        }
    }

    private function _checkIP($ip)
    {
        if (!empty($ip) && (preg_match('`^[\d]{1,3}\.[\d]{1,3}\.[\d]{1,3}\.[\d\/]{1,5}$`', $ip) || preg_match('/^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?(\/\d+)?\s*$/', $ip))) {
            return true;
        } else {
            return false;
        }
    }
}

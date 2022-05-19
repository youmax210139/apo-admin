<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Service\API\FrontMenu as FrontMenuApi;
use Service\Models\FrontMenu as FrontMenuModel;
use Cache;

class FrontmenuController extends Controller
{
    protected $fields = [
        'name' => '',
        'ident' => '',
        'category' => '',
        'status' => 1,
    ];

    protected $category_array = [
        'common' => '共用',
        'pc' => 'PC端',
        'h5' => 'H5端',
    ];

    public function getIndex(Request $request)
    {
        $data = [
            'category' => $request->get('category', ''),
            'category_array' => $this->category_array,
            'last_refresh_at' => Cache::store('redis')->get('frontMenuDataRefreshAt', ''),
        ];
        return view('front-menu.index', $data);
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
            $category = $request->get('category', '');
            $where_array = [];
            if ($category) {
                $where_array[] = ['category', '=', $category];
            }
            $data['recordsTotal'] = FrontMenuModel::where($where_array)->count();
            $select_array = ['id', 'name', 'ident', 'category', 'status', 'updated_at'];
            if (!empty($search['value'])) {
                $data['recordsFiltered'] = FrontMenuModel::where($where_array)->where(function ($query) use ($search) {
                    $query
                        ->where('ident', 'LIKE', '%' . $search['value'] . '%')
                        ->orWhere('name', 'LIKE', '%' . $search['value'] . '%');
                })
                    ->count();
                $data['data'] = FrontMenuModel::select($select_array)->where($where_array)->where(function ($query) use ($search) {
                    $query
                        ->where('ident', 'LIKE', '%' . $search['value'] . '%')
                        ->orWhere('name', 'LIKE', '%' . $search['value'] . '%');
                })
                    ->skip($start)->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->get();
            } else {
                $data['recordsFiltered'] = $data['recordsTotal'];
                $data['data'] = FrontMenuModel::select($select_array)->where($where_array)->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->skip($start)->take($length)
                    ->get();
            }
            if ($data['data']) {
                foreach ($data['data'] as $key => $val) {
                    $data['data'][$key]->category = isset($this->category_array[$val->category]) ? $this->category_array[$val->category] : $val->category;
                }
            }
            return response()->json($data);
        }
    }

    public function getCreate(Request $request)
    {
        $data = ['id' => 0];
        foreach ($this->fields as $field => $default) {
            $data[$field] = old($field, $default);
        }
        $data['category_array'] = $this->category_array;
        return view('front-menu.menu-form', $data);
    }

    public function postCreate(Request $request)
    {
        $name = $request->get('name', '');
        $ident = $request->get('ident', '');
        $status = (int)$request->get('status', 1);
        $category = $request->get('category', '');
        if (empty($name)) {
            $data = [
                'status' => -1,
                'msg' => '中文名称不能为空',
            ];
            return response()->json($data);
        }
        if (empty($ident)) {
            $data = [
                'status' => -1,
                'msg' => '英文标识不能为空',
            ];
            return response()->json($data);
        }
        if (!preg_match('/^[a-z\d\-_]{3,32}$/', $ident)) {
            $data = [
                'status' => -1,
                'msg' => '英文标识只能使用小写字母、数字、下划线，中横线，最少3个字符',
            ];
            return response()->json($data);
        }
        if (!isset($this->category_array[$category])) {
            $data = [
                'status' => -1,
                'msg' => '请选择正确的类别',
            ];
            return response()->json($data);
        }

        $check = FrontMenuModel::select(['id'])->where('ident', $ident)->first();
        if ($check) {
            $data = [
                'status' => -1,
                'msg' => $ident . ' 英文标识已经存在，请换一个。',
            ];
            return response()->json($data);
        }
        $model = new FrontMenuModel();
        $model->name = $name;
        $model->ident = $ident;
        $model->category = $category;
        $model->status = $status;
        if ($model->save()) {
            $data = [
                'status' => 0,
                'msg' => '保存菜单种类成功',
            ];
            return response()->json($data);
        } else {
            $data = [
                'status' => -1,
                'msg' => '保存菜单种类失败',
            ];
            return response()->json($data);
        }
    }

    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id', 0);
        if (empty($id)) {
            $data = [
                'status' => -1,
                'msg' => "{$id} id不存在",
            ];
            return response()->json($data);
        }
        $row = FrontMenuModel::select(array_keys($this->fields))->where('id', $id)->first();
        $data = ['id' => $id];
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $row->$field);
        }
        $data['category_array'] = $this->category_array;

        return view('front-menu.menu-form', $data);
    }

    public function postEdit(Request $request)
    {
        $id = (int)$request->get('id', 0);
        if (empty($id)) {
            $data = [
                'status' => -1,
                'msg' => '菜单种类英文标识不能为空',
            ];
            return response()->json($data);
        }
        $row = FrontMenuModel::where('id', $id)->first();
        if (empty($row)) {
            $data = [
                'status' => -1,
                'msg' => '菜单种类不存在',
            ];
            return response()->json($data);
        }
        //只是设置状态
        $set_status = (int)$request->get('set_status', 0);
        if ($set_status) {
            $row->status = $row->status > 0 ? 0 : 1;
            if ($row->save()) {
                $data = [
                    'status' => 0,
                    'msg' => '设置状态成功',
                ];
                return response()->json($data);
            } else {
                $data = [
                    'status' => -1,
                    'msg' => '设置状态失败',
                ];
                return response()->json($data);
            }
        }

        //修改内容
        $name = $request->get('name', '');
        $ident = $request->get('ident', '');
        $status = (int)$request->get('status', 1);
        $category = $request->get('category', '');
        $check = FrontMenuModel::select(['id'])->where('ident', $ident)->where('id', '!=', $id)->first();
        if ($check) {
            $data = [
                'status' => -1,
                'msg' => $ident . ' 英文标识已经存在，请换一个。',
            ];
            return response()->json($data);
        }

        if (empty($ident)) {
            $data = [
                'status' => -1,
                'msg' => '菜单种类英文标识不能为空',
            ];
            return response()->json($data);
        }
        if (!preg_match('/^[a-z\d\-_]{3,32}$/', $ident)) {
            $data = [
                'status' => -1,
                'msg' => '菜单种类英文标识只能使用小写字母、数字、下划线、中横线，最少3个字符',
            ];
            return response()->json($data);
        }
        if (empty($name)) {
            $data = [
                'status' => -1,
                'msg' => '菜单种类中文名称不能为空',
            ];
            return response()->json($data);
        }

        $row->ident = $ident;
        $row->name = $name;
        $row->category = $category;
        $row->status = $status;
        if ($row->save()) {
            $data = [
                'status' => 0,
                'msg' => '保存菜单种类成功',
            ];
            return response()->json($data);
        } else {
            $data = [
                'status' => -1,
                'msg' => '保存菜单种类失败',
            ];
            return response()->json($data);
        }
    }

    public function getEditData(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $menu_row = FrontMenuModel::find($id);
        if (empty($menu_row)) {
            return redirect()->back()->withErrors("{$id} 菜单记录不存在");
        }
        $data = [
            'menu_row' => $menu_row,
            'last_edit_at' => empty($menu_row) ? '' : $menu_row->last_editor,
        ];
        return view('front-menu.editdata', $data);
    }

    public function postEditData(Request $request)
    {
        $id = (int)$request->get('id', '');
        if (empty($id)) {
            $data = [
                'status' => -1,
                'msg' => '保存失败：' . $id . ' id不存在',
            ];
            return response()->json($data);
        }
        $row = FrontMenuModel::where('id', $id)->first();
        if (empty($row)) {
            $data = [
                'status' => -1,
                'msg' => '保存失败：' . $id . ' id不存在',
            ];
            return response()->json($data);
        }

        $data = $request->get('data', '');
        if (empty($data)) {
            $data = [
                'status' => -1,
                'msg' => '保存失败：json 数据为空',
            ];
            return response()->json($data);
        }
        $data = json_decode($data, true);
        if (empty($data)) {
            $data = [
                'status' => -1,
                'msg' => '保存失败：数据不符合json格式',
            ];
            return response()->json($data);
        }
        $cate_sort_array = [];
        foreach ($data as $ckey => &$cate) {
            if (empty($cate['name'])) {
                $data = [
                    'status' => -1,
                    'msg' => "保存失败：第 " . $ckey . " 个分类的名称为空",
                ];
                return response()->json($data);
            }
            if (empty($cate['path'])) {
                $data = [
                    'status' => -1,
                    'msg' => "保存失败：第 " . $ckey . " 个分类（{$cate['name']}）的标识为空",
                ];
                return response()->json($data);
            }
            $cate['name'] = isset($cate['name']) ? trim($cate['name']) : '';
            $cate['path'] = isset($cate['path']) ? trim($cate['path']) : '';
            $cate['sort'] = isset($cate['sort']) ? intval($cate['sort']) : 0;
            $cate['children'] = isset($cate['children']) ? $cate['children'] : [];
            $cate_sort_array[$ckey] = $cate['sort'];

            //子菜单
            $children_sort_array = [];
            foreach ($cate['children'] as $cdkey => &$item) {
                if (empty($item['name'])) {
                    $data = [
                        'status' => -1,
                        'msg' => "保存失败：第 " . $ckey . " 个分类的 第 " . $cdkey . " 菜单名称为空",
                    ];
                    return response()->json($data);
                }
                if (empty($item['path'])) {
                    $data = [
                        'status' => -1,
                        'msg' => "保存失败：第 " . $ckey . " 个分类的 第 " . $cdkey . " 菜单（{$item['name']}）路径为空",
                    ];
                    return response()->json($data);
                }
                $item['name'] = isset($item['name']) ? trim($item['name']) : '';
                $item['path'] = isset($item['path']) ? trim($item['path']) : '';
                $item['sort'] = isset($item['sort']) ? intval($item['sort']) : 0;
                $item['logo_pc'] = isset($item['logo_pc']) ? trim($item['logo_pc']) : '';
                $item['logo_h5'] = isset($item['logo_h5']) ? trim($item['logo_h5']) : '';
                $item['isnew'] = isset($item['isnew']) ? intval($item['isnew']) : 0;
                $item['ishot'] = isset($item['ishot']) ? intval($item['ishot']) : 0;
                $item['ishot2'] = isset($item['ishot2']) ? intval($item['ishot2']) : 0;
                $item['isguan'] = isset($item['isguan']) ? intval($item['isguan']) : 0;
            }
            if (!empty($children_sort_array)) {
                array_multisort($children_sort_array, $cate['children']);
            }
        }
        if (!empty($data)) {
            array_multisort($cate_sort_array, $data);
        }

        $row->data = json_encode($data, JSON_UNESCAPED_UNICODE);
        $row->last_editor = auth()->user()->username . '|' . Carbon::now()->format('Y-m-d H:i:s');
        $result = $row->save();
        if ($result) {
            $data = [
                'status' => 0,
                'msg' => "保存 【{$row->name}】 菜单成功",
            ];
            return response()->json($data);
        } else {
            $data = [
                'status' => -1,
                'msg' => "保存 【{$row->name}】 菜单失败",
            ];
            return response()->json($data);
        }
    }

    public function getRefresh()
    {
        $result = FrontMenuApi::refreshAllCache();
        if ($result) {
            Cache::store('redis')->forever('frontMenuDataRefreshAt', auth()->user()->username . "|" . date('Y-m-d H:i:s'));
            return redirect()->back()->withSuccess("刷新菜单缓存成功，前台显示需要等待约2分钟");
        } else {
            return redirect()->back()->withErrors("刷新菜单缓存失败");
        }
    }

    public function postMenuDelete(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $row = FrontMenuModel::find($id);
        if ($row && $row->delete()) {
            $data = [
                'status' => 0,
                'msg' => "删除成功",
            ];
            return response()->json($data);
        } else {
            $data = [
                'status' => -1,
                'msg' => "删除失败",
            ];
            return response()->json($data);
        }
    }

    public function getOutput(Request $request)
    {
        $id = (int)$request->get('id', 0);
        if ($id) {
            $rows = FrontMenuModel::where('id', $id)->orderBy('id', 'asc')->get();
            if (empty($rows)) {
                return redirect()->back()->withErrors("{$id} 记录不存在");
            }
            $name_pre = $rows[0]->ident;
        } else {
            $rows = FrontMenuModel::orderBy('id', 'asc')->get();
            $name_pre = 'all';
        }
        $string = '';
        $line = "// ===============================================================";
        foreach ($rows as $row) {
            $string .= $line . PHP_EOL;
            $string .= "// {$row->name} {$row->ident}" . PHP_EOL;
            $string .= $line . PHP_EOL;
            $string .= $row->data . PHP_EOL;
            $string .= PHP_EOL . PHP_EOL;
        }
        $file_name = $name_pre . '_front_menu_' . Carbon::now()->format('YmdHis') . '.json';
        return response()->streamDownload(function () use ($string) {
            echo $string;
        }, $file_name);
    }
}

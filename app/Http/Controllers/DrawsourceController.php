<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DrawsourceCreateRequest;
use App\Http\Requests\DrawsourceUpdateRequest;
use Service\Models\Lottery;
use Service\Models\Drawsource;
use Service\API\Drawsource\Catcher;

class DrawsourceController extends Controller
{
    protected $fields = [
        'lottery_id' => 0,
        'ident' => '',
        'name' => '',
        'url' => '',
        'status' => 1,
        'rank' => 100,
    ];

    public function getIndex(Request $request)
    {
        $method_catid = (int)$request->get('mcid', 0);
        $special = (int)$request->get('special', 0);
        $where_array = [];
        if ($method_catid > 0) {
            $where_array[] = ['lottery.lottery_method_category_id', '=', $method_catid];
        }
        if ($special > 0) {
            $where_array[] = ['lottery.special', '>', 0];
        }

        $all_rows = Drawsource::leftJoin('lottery', 'lottery.id', '=', 'drawsource.lottery_id')
            ->select('drawsource.*', 'lottery.name as lottery_name', 'lottery.ident as lottery_ident')
            ->where($where_array)
            ->orderBy('lottery.id', 'ASC')
            ->orderBy('drawsource.id', 'ASC')
            ->get();
        $data = ['drawsource' => []];
        foreach ($all_rows as $row) {
            $data['drawsource'][$row['lottery_id']][] = $row;
        }
        //玩法分类
        $data['method_category_rows'] = \Service\Models\LotteryMethodCategory::where('parent_id', '=', 0)
            ->orderBy('id', 'asc')
            ->get();
        $data['mcid'] = $method_catid;
        $data['special'] = $special;
        return view('drawsource.index', $data);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function postIndex(Request $request)
    {
        if ($request->get('test', '') == 1) {
            $lottery = Lottery::select(['lottery.id', 'lottery.ident', 'lottery.name', 'lottery.special', 'lottery.special_config', 'lottery_method_category.ident as type', 'lottery.issue_rule'])
                ->leftJoin('lottery_method_category', 'lottery_method_category.id', 'lottery.lottery_method_category_id')
                ->where('lottery.id', $request->get('lottery_id'))->first();
            $catcher = new Catcher();
            $result = $catcher->doCatch($request->get('lottery_id'), '', '', $request->get('id'), $lottery);

            return $result;
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
        $data['lottery'] = Lottery::orderBy('id', 'asc')->get();
        return view('drawsource.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param DrawsourceCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(DrawsourceCreateRequest $request)
    {
        $row = new Drawsource();
        foreach (array_keys($this->fields) as $field) {
            $row->$field = $request->get($field);
        }
        $row->save();
        return redirect('/drawsource/')->withSuccess('添加成功');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $row = Drawsource::find($id);
        if (!$row) {
            return redirect('/drawsource/')->withErrors("找不到该记录");
        }
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $row->$field);
        }
        $data['lottery'] = Lottery::orderBy('id', 'asc')->get();
        $data['id'] = (int)$id;
        return view('drawsource.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function putEdit(DrawsourceUpdateRequest $request)
    {
        $row = Drawsource::find((int)$request->get('id', 0));
        if (!$row) {
            return redirect('/drawsource/')->withErrors("找不到该记录");
        }
        if ($request->get('set_status', '') == '1') {
            $row->status = (int)$request->get('status', 0);
            $row->save();
            $status_txt = $row->status == 1 ? '启用' : '禁用';
            return response()->json(['status' => 0, 'msg' => "{$status_txt} {$row->name} 成功"]);
        } else {
            foreach (array_keys($this->fields) as $field) {
                $row->$field = $request->input($field);
            }
        }
        $row->save();
        return redirect('/drawsource/')->withSuccess('修改成功');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function deleteIndex(Request $request)
    {
        $row = Drawsource::find((int)$request->get('id', 0));

        if ($row) {
            $row->delete();

            return redirect()->back()->withSuccess("删除成功");
        }

        return redirect()->back()->withErrors("删除失败");
    }
}

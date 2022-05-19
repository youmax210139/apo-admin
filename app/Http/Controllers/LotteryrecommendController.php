<?php

namespace App\Http\Controllers;

use App\Http\Requests\LotteryRecommandCreateRequest;
use App\Http\Requests\LotteryRecommandUpdateRequest;
use Illuminate\Http\Request;
use Service\Models\Lottery;
use Service\Models\LotteryRecommend;
use Illuminate\Support\Facades\DB;

class LotteryrecommendController extends Controller
{
    protected $fields = [
        'lottery_id' => 0,
        'data' => '[]',
        'tip' => '',
        'interval_minutes' => 0,
        'status' => 1,
    ];

    public function getIndex()
    {
        $data = [];
        $data['lottery_recommend'] = LotteryRecommend::select([
            'lottery_recommend.*',
            'lottery.name as name',
            DB::raw('lottery_recommend.data->>0 as lottery_ident1'),
            DB::raw('lottery_recommend.data->>1 as lottery_ident2'),
            DB::raw('lottery_recommend.data->>2 as lottery_ident3'),
            DB::raw('lottery_recommend.data->>3 as lottery_ident4'),
            'a.name as lottery_ident_name1',
            'b.name as lottery_ident_name2',
            'c.name as lottery_ident_name3',
            'd.name as lottery_ident_name4',
        ])->leftjoin('lottery', 'lottery_recommend.lottery_id', 'lottery.id')
            ->leftjoin('lottery as a', DB::raw('lottery_recommend.data->>0'), 'a.ident')
            ->leftjoin('lottery as b', DB::raw('lottery_recommend.data->>1'), 'b.ident')
            ->leftjoin('lottery as c', DB::raw('lottery_recommend.data->>2'), 'c.ident')
            ->leftjoin('lottery as d', DB::raw('lottery_recommend.data->>3'), 'd.ident')
            ->orderBy('lottery_recommend.id', 'asc')
            ->get();

        return view('lottery-recommend.index', $data);
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

        for ($i = 1; $i <= 4; $i++) {
            $data['lottery_ident' . $i] = '';
        }

        $data['lottery'] = Lottery::select('id', 'ident', 'name')->where('status', 1)
            ->orderBy('lottery_category_id', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return view('lottery-recommend.create', $data);
    }

    public function putCreate(LotteryRecommandCreateRequest $request)
    {
        $row = new LotteryRecommend();
        foreach (array_keys($this->fields) as $field) {
            if ($field == 'data') {
                $row->data = $this->collect_field($request);
            } else {
                $row->$field = $request->get($field);
            }
        }

        $row->save();
        return redirect('/Lotteryrecommend\/')->withSuccess('添加成功');
    }

    private function collect_field($request)
    {
        for ($i = 1; $i <= 4; $i++) {
            $get_value = $request->get('lottery_ident' . $i);
            if ($get_value) {
                $val[] = $get_value;
            }
        }

        return json_encode($val);
    }

    private function disperse_field(&$data, $vals)
    {
        $data['lottery_ident1'] = $data['lottery_ident2'] = $data['lottery_ident3'] = $data['lottery_ident4'] = '';
        $vals = json_decode($vals);
        foreach ($vals as $key => $val) {
            $data['lottery_ident' . ($key + 1)] = $val;
        }
    }

    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $row = LotteryRecommend::find($id);
        if (!$row) {
            return redirect('/Lotteryrecommend\/')->withErrors("找不到该记录");
        }

        $data = [];
        foreach (array_keys($this->fields) as $field) {
            if ($field == 'data') {
                $this->disperse_field($data, old($field, $row->$field));
            } else {
                $data[$field] = old($field, $row->$field);
            }
        }
        $data['id'] = $id;
        $data['lottery'] = Lottery::select('id', 'ident', 'name')->where('status', 1)
            ->orderBy('lottery_category_id', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        return view('lottery-recommend.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\LotteryRecommandUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function putEdit(LotteryRecommandUpdateRequest $request)
    {
        $row = LotteryRecommend::find((int)$request->get('id', 0));
        if (!$row) {
            return redirect('/Lotteryrecommend\/')->withErrors("找不到该记录");
        }
        foreach (array_keys($this->fields) as $field) {
            if ($field == 'data') {
                $row->data = $this->collect_field($request);
            } else {
                $row->$field = $request->get($field);
            }
        }
        $row->save();

        return redirect('/Lotteryrecommend\/')->withSuccess('修改成功');
    }

    public function putStatus(Request $request)
    {
        $row = LotteryRecommend::find((int)$request->get('id', 0));
        $status = (int)$request->get('status');
        if ($row) {
            $row->status = $status;
            if ($row->save()) {
                return redirect()->back()->withSuccess(($status ? "启用" : '禁用') . "成功");
            }
        }

        return redirect()->back()->withErrors("操作失败");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteIndex(Request $request)
    {
        $row = LotteryRecommend::find((int)$request->get('id', 0));
        if ($row) {
            $row->delete();
            return redirect()->back()->withSuccess("删除成功");
        }

        return redirect()->back()->withErrors("删除失败");
    }
}

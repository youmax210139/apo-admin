<?php

namespace App\Http\Controllers;

use App\Http\Requests\LotteryIntroduceCreateRequest;
use App\Http\Requests\LotteryIntroduceUpdateRequest;
use Illuminate\Http\Request;
use Service\Models\Lottery;
use Service\Models\LotteryIntroduce;

class LotteryintroduceController extends Controller
{
    protected $fields = [
        'subject' => '',
        'content' => '',
        'sort' => 0,
        'lottery_id' => 0,
        'status' => 1,
    ];

    public function getIndex()
    {
        $data['lottery_introduce'] = LotteryIntroduce::select([
            'lottery_introduce.id',
            'lottery_introduce.subject',
            'lottery.name as lottery_name',
            'lottery_introduce.sort',
            'lottery_introduce.status',
        ])->leftJoin('lottery', 'lottery.id', 'lottery_introduce.lottery_id')
            ->orderBy('lottery_id')
            ->orderBy('lottery_introduce.sort')
            ->get();

        return view('lottery-introduce.index', $data);
    }

    public function getCreate(Request $request)
    {
        $data = [];
        foreach ($this->fields as $field => $default) {
            $data[$field] = old($field, $default);
        }

        $type = $request->get('type');
        if ($type == 1) {
            $data['lottery'] = Lottery::select('id', 'name')
                ->orderBy('lottery_category_id')
                ->orderBy('id')
                ->get();
        }

        return view('lottery-introduce.create', $data);
    }

    public function putCreate(LotteryIntroduceCreateRequest $request)
    {
        $row = new LotteryIntroduce();
        foreach (array_keys($this->fields) as $field) {
            $row->$field = $request->get($field);
        }
        if ($row->lottery_id) {
            $exist_row = LotteryIntroduce::where('lottery_id', $row->lottery_id)->first();
            if ($exist_row) {
                return redirect()->back()->withErrors("该彩种已设置介绍");
            }
        }

        $row->save();

        if ($row->lottery_id) {
            $status = $row->status == 1 ? 1 : 2;
            Lottery::where('id', $row->lottery_id)->update(['introduce_status' => $status]);
        }

        return redirect('/Lotteryintroduce\/')->withSuccess('添加成功');
    }

    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $row = LotteryIntroduce::find($id);
        if (!$row) {
            return redirect('/Lotteryintroduce\/')->withErrors("找不到该记录");
        }

        $data = [];
        foreach (array_keys($this->fields) as $field) {
            $data[$field] = old($field, $row->$field);
        }
        $data['id'] = $id;

        if ($data['lottery_id']) {
            $data['lottery'] = Lottery::select('id', 'name')
                ->orderBy('lottery_category_id')
                ->orderBy('id')
                ->get();
        }

        return view('lottery-introduce.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\LotteryIntroduceUpdateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function putEdit(LotteryIntroduceUpdateRequest $request)
    {
        $row = LotteryIntroduce::find((int)$request->get('id', 0));
        if (!$row) {
            return redirect('/Lotteryintroduce\/')->withErrors("找不到该记录");
        }
        $old_lottery_id = $row->lottery_id;
        foreach (array_keys($this->fields) as $field) {
            $row->$field = $request->get($field);
        }

        if ($row->lottery_id) {
            $exist_row = LotteryIntroduce::where('lottery_id', $row->lottery_id)->where('id', '!=', $row->id)->first();
            if ($exist_row) {
                return redirect()->back()->withErrors("该彩种已设置介绍");
            }
        }

        $row->save();

        if ($row->lottery_id) {
            if ($row->lottery_id != $old_lottery_id) {
                Lottery::where('id', $old_lottery_id)->update(['introduce_status' => 0]);
            }
            $status = $row->status == 1 ? 1 : 2;
            Lottery::where('id', $row->lottery_id)->update(['introduce_status' => $status]);
        }

        return redirect('/Lotteryintroduce\/')->withSuccess('修改成功');
    }

    public function putStatus(Request $request)
    {
        $row = LotteryIntroduce::find((int)$request->get('id', 0));
        $status = (int)$request->get('status');
        if ($row) {
            $row->status = $status;
            if ($row->save()) {
                if ($row->lottery_id) {
                    $status = $row->status == 1 ? 1 : 2;
                    Lottery::where('id', $row->lottery_id)->update(['introduce_status' => $status]);
                }
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
        $id = (int)$request->get('id', 0);
        $row = LotteryIntroduce::find($id);
        if ($row) {
            $lottery_id = $row->lottery_id;
            $row->delete();
            if ($lottery_id) {
                Lottery::where('id', $row->lottery_id)->update(['introduce_status' => 0]);
            }
            return redirect()->back()->withSuccess("删除成功");
        }

        return redirect()->back()->withErrors("删除失败");
    }
}

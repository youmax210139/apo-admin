<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LotteryMethodCreateRequest;
use App\Http\Requests\LotteryMethodUpdateRequest;
use Illuminate\Support\Facades\DB;
use mysql_xdevapi\Exception;
use Service\Models\Lottery;
use Service\Models\LotteryMethod;
use Service\Models\LotteryMethodCategory;
use Service\Models\LotteryMethodLimit;

class LotterymethodlimitController extends Controller
{
    protected $fields = [
        'id' => null,
        'lottery_id' => 0,
        'lottery_method_id' => 0,
        'project_min' => 0,
        'project_max' => 0,
        'issue_max' => 0

    ];

    public function getIndex(Request $request)
    {
        $data = array();
        $method_categories = LotteryMethodCategory::orderby('id', 'asc')
            ->get(['id', 'parent_id', 'name']);

        $method_categories_group = [];
        foreach ($method_categories as $method_category) {
            if ($method_category->parent_id == 0) {
                $method_categories_group[$method_category->id]['name'] = $method_category->name;
            } else {
                $method_categories_group[$method_category->parent_id]['child'][]
                    = $method_category;
            }
        }
        $data['lottery_list'] = \Service\API\Lottery::getAllLotteryGroupByCategory();
        $data['method_categories'] = $method_categories_group;
        $data['id'] = (int)$request->get("id");
        return view('lottery-method-limit.index', $data);
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
            //$search = $request->get('search');
            $lottery_method_category_id = $request->get('lottery_method_category_id');
            $lottery_id = $request->get('lottery_id', 'all');
            $lottery_method_ident = trim($request->get('lottery_method_ident'));

            $where = array();

            if ($lottery_id != 'all') {
                $lottery_id = intval($lottery_id);
                $where[] = array('lottery_method_limit.lottery_id', '=', $lottery_id);
            }
            if ($lottery_method_category_id != 'all') {
                $where[] = array('lm.lottery_method_category_id', '=', $lottery_method_category_id);
            }

            if (!empty($lottery_method_ident)) {
                $where[] = array('lm.ident', 'LIKE', '%' . $lottery_method_ident . '%');
            }

            $data['recordsFiltered'] = $data['recordsTotal'] = LotteryMethodLimit::
            leftJoin('lottery_method as lm', 'lm.id', 'lottery_method_limit.lottery_method_id')
                ->leftJoin('lottery_method_category as lmc', 'lm.lottery_method_category_id', '=', 'lmc.id')
                ->leftJoin('lottery as l', 'l.id', 'lottery_method_limit.lottery_id')
                ->where($where)
                ->count();
            $data['data'] = LotteryMethodLimit::leftJoin('lottery_method as lm', 'lm.id', 'lottery_method_limit.lottery_method_id')
                ->leftJoin('lottery_method_category as lmc', 'lm.lottery_method_category_id', '=', 'lmc.id')
                ->leftJoin('lottery_method as lm2', 'lm.parent_id', '=', 'lm2.id')
                ->leftJoin('lottery_method as lm1', 'lm2.parent_id', '=', 'lm1.id')
                ->leftJoin('lottery as l', 'lottery_method_limit.lottery_id', '=', 'l.id')
                ->where($where)
                ->select(
                    [
                        'lottery_method_limit.*',
                        'lm.name',
                        'lm.ident',
                        'lmc.name as lottery_method_category_name',
                        DB::raw('(lm1.name || \' - \' || lm2.name || \' - \' || lm.name) as method_name'),
                        DB::raw('coalesce(l.name, \'所有\') AS lottery_name')
                    ]
                )
                ->skip($start)->take($length)
                ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                ->get();
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

        $flag = $request->get('flag', '');
        if ($flag == 'group') {
            $cate_id = (int)$request->get('cate_id', 0);
            $group = LotteryMethod::select([
                'lottery_method.id',
                DB::raw('(lm2.name || \' - \' || lottery_method.name) as name')
            ])
                ->leftJoin('lottery_method as lm2', 'lottery_method.parent_id', '=', 'lm2.id')
                ->where('lottery_method.lottery_method_category_id', $cate_id)
                ->where('lottery_method.parent_id', '>', 0)
                ->whereRaw("lottery_method.prize_level::text ='[]'")->get();
            return response()->json([
                'group' => $group,
                'lottery' => Lottery::select(['lottery.id', 'lottery.name'])
                    ->leftJoin('lottery_method_category as lmc', 'lmc.id', 'lottery.lottery_method_category_id')
                    ->where('lmc.id', ($cate_id % 2 == 0 ? ($cate_id - 2) : ($cate_id - 1)))->get()
            ]);
        }
        if ($flag == 'method') {
            $cate_id = (int)$request->get('cate_id', 0);
            $group = LotteryMethod::select([
                'lottery_method.id',
                'lottery_method.name'
            ])
                ->where('lottery_method.parent_id', $cate_id)
                ->get();
            return response()->json([
                'data' => $group
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LotteryMethodCreateRequest $request
     * @return \Illuminate\Http\Response
     */
    public function postCreate(Request $request)
    {
        $method_ids = $request->get('method_ids', 0);
        if (empty($method_ids)) {
            return response()->json([
                'status' => 1,
                'msg' => '请选择受限玩法'
            ]);
        }
        $project_min = (float)$request->get('project_min', 0);
        $project_max = (int)$request->get('project_max', 0);
        $issue_max = (int)$request->get('issue_max', 0);
        $issue_total_max = (int)$request->get('issue_total_max', 0);
        $max_bet_num = (int)$request->get('max_bet_num', 0);
        $lottery_id = (int)$request->get('lottery_id', 0);
        if (count($method_ids) <> LotteryMethod::whereIn('id', $method_ids)->count()) {
            return response()->json([
                'status' => 1,
                'msg' => '提交数据错误，玩法不存在！'
            ]);
        }
        $fails = [];
        $success = 0;
        foreach ($method_ids as $method_id) {
            $lottery_method_limit = new LotteryMethodLimit();
            $lottery_method_limit->lottery_method_id = $method_id;
            $lottery_method_limit->lottery_id = $lottery_id;
            $lottery_method_limit->project_min = $project_min;
            $lottery_method_limit->project_max = $project_max;
            $lottery_method_limit->issue_max = $issue_max;
            $lottery_method_limit->issue_total_max = $issue_total_max;
            $lottery_method_limit->max_bet_num = $max_bet_num;
            try {
                $lottery_method_limit->save();
                $success = $success + 1;
            } catch (\Exception $e) {
                $fails[] = $method_id;
            }
        }
        return response()->json([
            'status' => 0,
            'msg' => '操作成功！成功写入' . $success . '条，已存在限制' . count($fails) . '条' . implode(',', $fails)
        ]);
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
        $data = LotteryMethodLimit::select([
            'lottery_method_limit.*',
            'lm.name',
            'lm.ident',
            'lmc.name as lottery_method_category_name',
            DB::raw('(lm1.name || \' - \' || lm2.name || \' - \' || lm.name) as method_name'),
            DB::raw('coalesce(l.name, \'所有\') AS lottery_name')
        ])
            ->leftJoin('lottery_method as lm', 'lm.id', 'lottery_method_limit.lottery_method_id')
            ->leftJoin('lottery_method_category as lmc', 'lm.lottery_method_category_id', '=', 'lmc.id')
            ->leftJoin('lottery_method as lm2', 'lm.parent_id', '=', 'lm2.id')
            ->leftJoin('lottery_method as lm1', 'lm2.parent_id', '=', 'lm1.id')
            ->leftJoin('lottery as l', 'lottery_method_limit.lottery_id', '=', 'l.id')
            ->where('lottery_method_limit.id', $id)->first();
        if (!$data) {
            return redirect('/lotterymethod\/')->withErrors("找不到 id={$id} 的投注限制！");
        }

        return view('lottery-method-limit.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LotteryMethodUpdateRequest|Request $request
     * @return \Illuminate\Http\Response
     */
    public function putEdit(Request $request)
    {
        $flag = $request->get('flag', '');
        if ($flag == 'multi') {
            $ids = $request->get('ids', '');
            if (empty($ids)) {
                return response()->json([
                    'status' => 1,
                    'msg' => "请选择需要修改的玩法限制！"
                ]);
            }
            try {
                LotteryMethodLimit::whereIn('id', explode(',', $ids))->update([
                    'project_min' => (float)$request->get('project_min', 0),
                    'project_max' => (float)$request->get('project_max', 0),
                    'issue_max' => (float)$request->get('issue_max', 0),
                    'issue_total_max' => (float)$request->get('issue_total_max', 0),
                    'max_bet_num' => (float)$request->get('max_bet_num', 0)
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 1,
                    'msg' => $e->getMessage()
                ]);
            }
            return response()->json([
                'status' => 0,
                'msg' => "批量修改投注限制成功！"
            ]);
        }
        $id = (int)$request->get('id', 0);
        $row = LotteryMethodLimit::find($id);
        if (!$row) {
            return response()->json([
                'status' => 1,
                'msg' => "找不到 id={$id} 的投注限制！"
            ]);
        }

        $row->project_min = (float)$request->get('project_min', 0);
        $row->project_max = (int)$request->get('project_max', 0);
        $row->issue_max = (int)$request->get('issue_max', 0);
        $row->issue_total_max = (int)$request->get('issue_total_max', 0);
        $row->max_bet_num = (int)$request->get('max_bet_num', 0);
        try {
            $row->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'msg' => $e->getMessage()
            ]);
        }
        return response()->json([
            'status' => 0,
            'msg' => "修改 id={$id} 的投注限制成功！"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function deleteIndex(Request $request)
    {
        $flag = $request->get('flag', '');
        if ($flag == 'multi') {
            $ids = $request->get('ids', '');
            if (empty($ids)) {
                return response()->json([
                    'status' => 1,
                    'msg' => "请选择需要删除的玩法限制！"
                ]);
            }
            try {
                LotteryMethodLimit::destroy(explode(',', $ids));
                return response()->json([
                    'status' => 0,
                    'msg' => "批量删除成功！"
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'status' => 1,
                    'msg' => $e->getMessage()
                ]);
            }
        }
        $id = (int)$request->get('id', 0);
        $row = LotteryMethodLimit::find($id);
        if ($row) {
            LotteryMethodLimit::destroy($id);
            return redirect()->back()->withSuccess("删除 id={$id} 投注限制成功！");
        } else {
            return redirect()->back()->withErrors("删除失败，没有 id={$id} 的限制！");
        }
    }
}

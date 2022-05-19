<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Service\Models\Issue;
use Service\Models\Lottery;
use Service\Models\LotteryMethodCategory;
use Service\API\SendPrize\Draw\Draw;
use Illuminate\Support\Facades\Redis;

class DrawController extends Controller
{
    public function getIndex(Request $request)
    {
        $lottery_id = (int)$request->get('lottery_id', 1);
        $lottery = Lottery::find($lottery_id);
        if (!$lottery) {
            return redirect('/draw/')->withErrors("找不到该记录");
        }
        $data['method_categories'] = LotteryMethodCategory::select(['id', 'name'])->where('parent_id', 0)->get();
        $data['lotterys'] = Lottery::select(['id', 'name', 'lottery_method_category_id', 'special', 'status'])->orderBy('id', 'asc')->get();
        $data['lottery'] = $lottery;
        $data['lottery_id'] = $lottery_id;
        $data['category_id'] = $lottery->lottery_method_category_id;
        $data['is_special'] = $lottery->special;
        $issue = \Service\Models\Issue::where('lottery_id', $lottery_id)
            ->where('code_status', '<', 2)
            ->where('earliest_write_time', '<', DB::raw('LOCALTIMESTAMP'))
            ->orderBy('issue', 'asc')
            ->first();

        $data['wait_issue'] = $issue;
        $data['self_lottery_manual_input'] = get_config('self_lottery_manual_input', 0);
        return view('drawsource.draw', $data);
    }

    public function postIndex(Request $request)
    {
        $lottery_id = (int)$request->get('lottery_id', 1);
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->get('search');
        $data['recordsFiltered'] = $data['recordsTotal'] = Issue::where('lottery_id', $lottery_id)
            ->where('sale_end', '<=', DB::raw('LOCALTIMESTAMP'))
            ->where(function ($query) use ($search) {
                $query->where('issue', 'LIKE', '%' . $search['value'] . '%');
            })
            ->count();
        $data['data'] = Issue::where('lottery_id', $lottery_id)
            ->where('sale_end', '<=', DB::raw('LOCALTIMESTAMP'))
            ->where(function ($query) use ($search) {
                $query->where('issue', 'LIKE', '%' . $search['value'] . '%');
            })
            ->skip($start)->take($length)
            ->orderBy('sale_end', 'desc')
            ->get();
        return response()->json($data);
    }

    public function putEntercode(Request $request)
    {
        $lottery_id = (int)$request->get('lottery_id', 0);
        $issue = $request->get('issue', '');
        $code = $request->get('code', '');
        $issue_api = new \Service\API\Issue\Issue();

        //自主彩禁止录号
        $lottery = Lottery::select(['lottery.id', 'lottery.ident', 'lottery.special', 'lottery_method_category.ident as type'])
            ->leftJoin('lottery_method_category', 'lottery_method_category.id', 'lottery.lottery_method_category_id')
            ->where('lottery.id', $lottery_id)
            ->first();
        if (empty($lottery)) {
            return redirect()->back()->withErrors('彩种不存在');
        }
        if ($lottery->special == 1 && get_config('self_lottery_manual_input', 0) == 0) {
            return redirect()->back()->withErrors('自主彩禁止录号');
        }
        if ($lottery->special > 1) {
            return redirect()->back()->withErrors('秒秒彩禁止录号');
        }

        if ($issue_api->drawNumber($lottery_id, $issue, $code)) {
            //长龙统计
            $next_issue = Issue::where('issue', '>', $issue)
                ->where('lottery_id', $lottery_id)
                ->where('code_status', '<=', 2)
                ->orderBy('issue', 'asc')
                ->first(['issue']);
            $long = json_decode(Redis::get('long:' . $lottery->ident), true);
            if (empty($long)) {
                Draw::histroyLong($lottery->ident, 30, $next_issue ? $next_issue->issue : '');
            } else {
                Draw::long($lottery->ident, $lottery->type, $code, $next_issue ? $next_issue->issue : '');
            }
            return redirect('/draw\/index?lottery_id=' . $lottery_id)->withSuccess('录入成功');
        } else {
            return redirect()->back()->withErrors($issue_api->error_msg);
        }
    }
}

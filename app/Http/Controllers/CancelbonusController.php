<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Service\Models\Issue;

class CancelbonusController extends Controller
{
    public function getIndex()
    {
        $data['lotteries'] = \Service\API\Lottery::getAllLotteryGroupByCategory();
        return view('drawsource.cancelbonus', $data);
    }

    public function postIndex(Request $request)
    {
        $lottery_id = (int)$request->get('lottery_id', 0);
        $type = (int)$request->get('type', 0);
        $issue_exception_time = get_config('issue_exception_time', 24 * 60);
        $sale_start = date('Y-m-d H:i:s', strtotime("-$issue_exception_time minutes"));
        $issues = Issue::where('lottery_id', $lottery_id)
            ->where('sale_start', '>', $sale_start)
            ->where('sale_end', '<', date('Y-m-d H:i:s'))
            ->where(function ($query) use ($type) {
                if ($type == 3) {
                    $query->where('code_status', 0);
                } else {
                    $query->where('code_status', 2);
                }
            })
            ->orderBy('id', 'desc')
            ->get();
        return response()->json($issues);
    }

    public function putIndex(Request $request)
    {
        $lottery_id = (int)$request->get('lottery_id', 0);
        $issues = $request->get('issue', '');
        $error_type = (int)$request->get('type', 0);
        $start_timne = $request->get('start_time', '');
        $issue_code = $request->get('issue_code', '');
        if (empty($lottery_id) || empty($issues) || empty($error_type)) {
            return redirect()->back()->withErrors("数据不完整");
        }
        if (!in_array($error_type, array(1, 2, 3))) {
            return redirect()->back()->withErrors("数据错误");
        }
        $ext = '';
        if ($error_type == 1) {
            if (strtotime($start_timne) === false) {
                return redirect()->back()->withErrors("请输入正确开奖时间");
            }
            $ext = $start_timne;
        }
        if ($error_type == 2) {
            if (empty($issue_code)) {
                return redirect()->back()->withErrors("请输入正确开奖号码");
            }
            $ext = $issue_code;
        }
        $issue_api = new \Service\API\Issue\Issue();
        if ($error_type == 3) {
            $success = 0;
            foreach ($issues as $issue) {
                if ($issue_api->createErrorTask($lottery_id, $issue, $error_type, $ext)) {
                    $success++;
                }
            }
            return redirect()->back()->withSuccess("成功录入 {$success} 记录，失败" . (count($issues) - $success) . '条');
        } else {
            if ($issue_api->createErrorTask($lottery_id, $issues[0], $error_type, $ext)) {
                return redirect()->back()->withSuccess("成功录入，系统将在下一分钟处理");
            }
        }
        return redirect()->back()->withErrors($issue_api->error_msg);
    }
}

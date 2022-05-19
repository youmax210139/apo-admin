<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Service\Models\IssueError;
use Service\Models\Lottery;

class ErrorissueController extends Controller
{
    public function getIndex()
    {
        $data['lotterys'] = Lottery::all();
        return view('drawsource.errorissue', $data);
    }

    public function postIndex(Request $request)
    {
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->get('search');
        $data['recordsFiltered'] = $data['recordsTotal'] = IssueError::where(function ($query) use ($search) {
            $query->where('issue', 'LIKE', '%' . $search['value'] . '%');
        })->count();
        $data['data'] = IssueError::select([
            'issue_error.*',
            'lottery.name as lottery_name',
            'lottery.ident as lottery_ident',
        ])
            ->leftJoin('lottery', 'lottery.id', 'issue_error.lottery_id')
            ->where(function ($query) use ($search) {
                $query->where('issue_error.issue', 'LIKE', '%' . $search['value'] . '%');
            })
            ->skip($start)->take($length)
            ->orderBy('issue_error.write_time', 'desc')
            ->get();
        return response()->json($data);
    }

    public function putIndex(Request $request)
    {
        $lottery_id = (int)$request->get('lottery_id', 0);
        $issue = $request->get('issue', '');
        $error_type = (int)$request->get('type', 0);
        $start_timne = $request->get('start_timne', '');
        $issue_code = $request->get('issue_code', '');
        if (empty($lottery_id) || empty($issue) || empty($error_type)) {
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
        if ($issue_api->createErrorTask($lottery_id, $issue, $error_type, $ext)) {
            return redirect()->back()->withSuccess("开奖异常处理录入成功！");
        }
        return redirect()->back()->withErrors($issue_api->error_msg);
    }

    /**
     * 重置异常状态
     * @param Request $request
     */
    public function postReset(Request $request)
    {
        $id = $request->get('id', 0);
        $issue_error = IssueError::find($id);
        if (!$issue_error) {
            return response()->json(['msg' => '记录不存在', 'status' => 1]);
        }
        try {
            $issue_error->old_code_status = 0;
            $issue_error->old_deduct_status = 0;
            $issue_error->old_rebate_status = 0;
            $issue_error->old_checkbonus_status = 0;
            $issue_error->old_bonus_status = 0;
            $issue_error->old_tasktoproject_status = 0;
            $issue_error->code_status = 0;
            $issue_error->deduct_status = 0;
            $issue_error->rebate_status = 0;
            $issue_error->check_bonus_status = 0;
            $issue_error->bonus_status = 0;
            $issue_error->task_to_project_status = 0;
            $issue_error->cancel_bonus_status = 0;
            $issue_error->repeal_status = 0;
            $issue_error->save();
            return response()->json(['msg' => '操作成功', 'status' => 0]);
        } catch (\Exception $e) {
            return response()->json(['msg' => $e->getMessage(), 'status' => 1]);
        }
    }
}

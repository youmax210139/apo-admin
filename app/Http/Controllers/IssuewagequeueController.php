<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\IssuewagequeueIndexRequest;
use Service\Models\IssueWageQueue;
use Service\Models\Lottery;

class IssuewagequeueController extends Controller
{
    public function getIndex()
    {
        return view('issue-wage-queue.index', [
            'start_date' => '',
            'end_date' => '',
            'lottery_list' => \Service\API\Lottery::getAllLotteryGroupByCategory(),
        ]);
    }

    public function postIndex(IssuewagequeueIndexRequest $request)
    {
        if ($request->ajax()) {
            $param['start_date'] = $request->get('start_date');
            $param['end_date'] = $request->get('end_date');
            $param['lottery_id'] = $request->get('lottery_id');
            $param['lottery_status'] = $request->get('lottery_status');
            $param['wage_type'] = $request->get('wage_type', 0);
            $param['queue_status'] = $request->get('queue_status');

            if ($param['queue_status'] == 2 && empty($param['wage_type'])) {
                //如果选择了查询未派彩种，需要指定类型
                return response()->json([
                    'errors' => [
                        'wage_type' => ['队列状态定指为未派发时，需要指定工资类型'],
                    ],
                    'message' => 'The given data was invalid.',
                ], 422);
            }
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');
            $data = [];
            if ($param['queue_status'] == 1) {
                //已经派发
                //查询条件
                $where = function ($query) use ($param) {
                    if (!empty($param['start_date'])) {
                        $query->where('issue_wage_queue.updated_at', '>=', $param['start_date']);
                    }
                    if (!empty($param['end_date'])) {
                        $query->where('issue_wage_queue.updated_at', '<=', $param['end_date']);
                    }
                    if (!empty($param['lottery_id'])) {
                        $query->where('issue_wage_queue.lottery_id', $param['lottery_id']);
                    }
                    if (!empty($param['lottery_status'])) {
                        $query->where('lottery.status', $param['lottery_status']);
                    }
                    if (!empty($param['wage_type'])) {
                        $query->where('issue_wage_queue.type', '=', $param['wage_type']);
                    }
                    if ($param['queue_status'] == 2) {
                        $query->where('issue_wage_queue.issue', '=', '');
                    }
                };
                $issue_wage_queue = IssueWageQueue::leftJoin('lottery', 'issue_wage_queue.lottery_id', 'lottery.id')
                    ->leftJoin('issue', function ($join) {
                        $join->on('issue.lottery_id', '=', 'issue_wage_queue.lottery_id')
                            ->on('issue.issue', '=', 'issue_wage_queue.issue');
                    })
                    ->where($where);
                $data['recordsTotal'] = $data['recordsFiltered'] =
                    $issue_wage_queue->count();
                $data['data'] = [];
                if ($data['recordsTotal'] > 0) {
                    $queue_results = $issue_wage_queue->select([
                        'issue_wage_queue.type AS wage_type',
                        'issue_wage_queue.issue AS lottery_issue',
                        'issue_wage_queue.created_at AS created_at',
                        'issue_wage_queue.updated_at AS updated_at',
                        'lottery.name AS lottery_name',
                        'lottery.ident AS lottery_ident',
                        'lottery.status AS lottery_status',
                        'issue.sale_end AS issue_sale_end'
                    ])
                        ->skip($start)->take($length)
                        ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                        ->get()->toArray();
                    $data['data'] = $queue_results;
                }
                return $data;
            } else {
                //未派发
                $where = function ($query) use ($param) {
                    if (!empty($param['lottery_id'])) {
                        $query->where('lottery.id', $param['lottery_id']);
                    }
                    if (!empty($param['lottery_status'])) {
                        $query->where('lottery.status', $param['lottery_status']);
                    }
                };
                $lottery = Lottery::whereNotIn('id', function ($query) use ($param) {
                    $query->select('lottery_id')->from('issue_wage_queue')->where('type', $param['wage_type']);
                })->where($where);
                $data['recordsTotal'] = $data['recordsFiltered'] = $lottery->count();
                $data['data'] = [];
                if ($data['recordsTotal'] > 0) {
                    $order_columns = 'lottery_id';
                    if (in_array($columns[$order[0]['column']]['data'], ['lottery_name', 'lottery_ident', 'lottery_status'])) {
                        $order_columns = $columns[$order[0]['column']]['data'];
                    }
                    $lottery_results = $lottery->select([
                        'lottery.id AS lottery_id',
                        'lottery.name AS lottery_name',
                        'lottery.ident AS lottery_ident',
                        'lottery.status AS lottery_status',
                    ])
                        ->skip($start)->take($length)
                        ->orderBy($order_columns, $order[0]['dir'])
                        ->get()->toArray();
                    foreach ($lottery_results as &$lottery_result) {
                        $lottery_result['wage_type'] = $param['wage_type'];
                        $lottery_result['lottery_issue'] = '';
                        $lottery_result['created_at'] = '';
                        $lottery_result['updated_at'] = '';
                        $lottery_result['sale_end'] = '';
                    }
                    $data['data'] = $lottery_results;
                }
                return $data;
            }
        }
    }
}

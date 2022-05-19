<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Service\Models\ReportLotteryCompressed;
use Service\Models\Lottery as LotteryModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LotterysinglesaleController extends Controller
{
    public function getIndex()
    {
        $data['lottery_list'] = LotteryModel::all();
        $data['start_date'] = Carbon::today();
        return view('report/single-sale', $data);
    }

    public function postIndex(Request $request)
    {
        if ($request->ajax() || $request->get('export', 0)) {
            $export = (int)$request->get('export', 0);
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');
            $param['start_date'] = $request->post('start_date', Carbon::today());
            $param['end_date'] = $request->post('end_date', Carbon::today()->endOfDay());
            $param['lottery_id'] = (int)$request->post('lottery_id');
            $param['user_group_id'] = (int)$request->post('user_group_id', 0);
            //查询条件
            $where = function ($query) use ($param) {
                //下单时间对比
                if (!empty($param['start_date'])) {
                    $query->where('report_lottery_compressed.created_at', '>=', $param['start_date']);
                }
                if (!empty($param['end_date'])) {
                    $query->where('report_lottery_compressed.created_at', '<=', $param['end_date']);
                }
                if (!empty($param['lottery_id'])) {
                    $query->where('lottery_id', $param['lottery_id']);
                }
                if (!empty($param['user_group_id'])) {
                    $query->where('users.user_group_id', $param['user_group_id']);
                }
            };
            $sub_table = ReportLotteryCompressed::select([
                DB::raw('sum(price) as sum_sell'),
                'issue',
                'lottery_id',
                DB::raw('sum(rebate) as sum_rebate'),
                DB::raw('sum(bonus) as sum_bonus'),
            ])->leftJoin('users', 'users.id', 'report_lottery_compressed.user_id')
                ->where($where)->groupBy(['lottery_id', 'issue']);
            $data['recordsTotal'] = $data['recordsFiltered'] = DB::table(DB::raw("({$sub_table->toSql()}) as sub"))
                ->mergeBindings($sub_table->getQuery())
                ->count();
            $data['data'] = DB::table(DB::raw("({$sub_table->toSql()}) as sub"))
                ->select([
                    'sub.*',
                    'lottery.name as lottery_name',
                    'issue.write_time',
                    'issue.sale_end',
                    'issue.belong_date as date',
                    'issue.code',
                    DB::raw('(sum_sell-sum_rebate-sum_bonus) as total_sum'),
                ])
                ->leftJoin('issue', function ($join) {
                    $join->on('issue.issue', '=', 'sub.issue')
                        ->on('issue.lottery_id', '=', 'sub.lottery_id');
                })
                ->leftJoin('lottery', 'lottery.id', 'sub.lottery_id')
                ->mergeBindings($sub_table->getQuery());

            if (empty($export)) {
                $data['data'] = $data['data']->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->skip($start)->take($length)
                    ->get();
                //统计所有信息
                $sum_amount = DB::table(DB::raw("({$sub_table->toSql()}) as sub"))
                    ->select([DB::RAW('sum(sub.sum_sell) as sell'),
                        DB::RAW('sum(sub.sum_rebate) as rebate'),
                        DB::RAW('sum(sub.sum_bonus) as bonus')])
                    ->mergeBindings($sub_table->getQuery())
                    ->first();
                $data['sum_amount'] = ['sell' => 0, 'rebate' => 0, 'bonus' => 0, 'total_sum' => 0];
                if ($sum_amount) {
                    $sum_amount->total_sum = $sum_amount->sell - $sum_amount->rebate - $sum_amount->bonus;
                    $data['sum_amount'] = $sum_amount;
                }
                return $data;
            } else {
                //导出数据
                $file_name = date('Ymd-H_i_s') . "-彩票单期盈亏报表.csv";
                $query = $data['data'];
                $response = new StreamedResponse(null, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
                ]);

                $response->setCallback(function () use ($query) {
                    $out = fopen('php://output', 'w');
                    fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // 添加 BOM
                    $first = true;
                    $query->orderBy('sum_sell', 'desc')->orderBy('issue', 'desc')->chunk(500, function ($results) use (&$first, $out) {
                        if ($first) {
                            //列名
                            $columnNames[] = '日期';
                            $columnNames[] = '彩种';
                            $columnNames[] = '奖期';
                            $columnNames[] = '截止投注';
                            $columnNames[] = '开奖时间';
                            $columnNames[] = '总销售额';
                            $columnNames[] = '总返点';
                            $columnNames[] = '总返奖';
                            $columnNames[] = '亏损值';
                            $columnNames[] = '开奖号码';
                            fputcsv($out, $columnNames);
                            $first = false;
                        }
                        $datas = [];
                        foreach ($results as $item) {
                            $datas[] = [
                                $item->date,
                                $item->lottery_name,
                                $item->issue,
                                $item->sale_end,
                                $item->write_time,
                                $item->sum_sell,
                                $item->sum_rebate,
                                $item->sum_bonus,
                                ($item->sum_sell - $item->sum_rebate - $item->sum_bonus),
                                $item->code,
                            ];
                        }
                        foreach ($datas as $item) {
                            fputcsv($out, $item);
                        }
                    });
                    fclose($out);
                });
                $response->send();
            }
        }
    }
}

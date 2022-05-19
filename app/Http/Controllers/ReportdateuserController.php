<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportDateUserIndexRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Service\Models\ReportDateUser;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportdateuserController extends Controller
{
    public function getIndex()
    {
        return view(
            'report-date-user/index',
            [
                'lottery_list' => \Service\API\Lottery::getAllLotteryGroupByCategory(),
                'start_date' => Carbon::yesterday()->format("Y-m-d"),
                'end_date' => Carbon::today()->format("Y-m-d")
            ]
        );
    }

    public function postIndex(ReportDateUserIndexRequest $request)
    {
        $data = array();
        $data['draw'] = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $order = $request->get('order');
        $export = (int)$request->get('export', 0);

        $param['start_time'] = $request->get('start_time');
        $param['end_time'] = $request->get('end_time');
        $where[] = ['date', '>=', $param['start_time']];
        $where[] = ['date', '<=', $param['end_time']];

        $model = ReportDateUser::where($where);
        if ($export) {
            //导出数据
            $file_name = "每日用户报表{$param['start_time']}-{$param['end_time']}.csv";
            $response = new StreamedResponse(null, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
            ]);

            $response->setCallback(function () use ($model) {
                $out = fopen('php://output', 'w');
                fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // 添加 BOM
                //列名
                $columnNames = [
                    '日期',
                    '登陆用户数',
                    '充值人数',
                    '充值人次',
                    '提现人数',
                    '首次绑卡人数',
                    '首充人数',
                    '首充金额',
                    '二充人数',
                    '新增用户数',
                    '投注人数',
                    '活跃用户数',
                    '活跃代理数',
                    '第三方投注人数',
                    '第三方活跃用户数',
                    '第三方活跃代理数',
                ];
                fputcsv($out, $columnNames);
                $model->orderBy("date", 'desc')->chunk(500, function ($results) use (&$first, $out) {
                    $datas = [];
                    foreach ($results as $item) {
                        $data = json_decode($item->data);
                        $datas[] = [
                            $item->date,
                            $data->login_user,
                            $data->deposit_user,
                            $data->deposit_number,
                            $data->withdrawals_user ?? '-',
                            $data->first_user_bind_bank ?? '-',
                            $data->first_deposit_user,
                            $data->first_deposit_sum ?? '-',
                            $data->second_deposit_user,
                            $data->new_user,
                            $data->bet_user,
                            $data->active_user,
                            $data->active_proxy,
                            $data->third_bet_user,
                            $data->third_active_user,
                            $data->third_active_proxy,
                        ];
                    }
                    foreach ($datas as $item) {
                        fputcsv($out, $item);
                    }
                });
                fclose($out);
            });
            $response->send();
        } else {
            $total = $model->count();
            $data['recordsTotal'] = $data['recordsFiltered'] = $total;
            $data['data'] = $model->skip($start)->take($length)->orderBy('date', $order[0]['dir'])->get();
            foreach ($data['data'] as $_k => $_row) {
                $data['data'][$_k]['data'] = json_decode($_row->data);
            }
            return response()->json($data);
        }
    }
}

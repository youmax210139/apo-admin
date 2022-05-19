<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Http\Requests\DailyDeviceIndexRequest;
use Service\Models\ReportDailyDevice;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\DB;

class DailydeviceController extends Controller
{
    public function getIndex()
    {
        $data = [
            'source_list' => get_client_types(),
            'start_date' => Carbon::yesterday()->toDateString(),
            'end_date' => Carbon::today()->toDateString()
        ];
        return view('report-daily-device/index', $data);
    }

    public function postIndex(DailyDeviceIndexRequest $request)
    {
        if ($request->ajax() || $request->get('export', 0)) {
            $data = [
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'sum_total' => []
            ];
            //查询数据逻辑
            $export = (int)$request->get('export', 0);
            $client_type = $request->get('client_type', 'all');
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $start_date = $request->post('start_date');
            $end_date = $request->post('end_date');
            $columns = $request->get('columns');
            $model = ReportDailyDevice::query()
                ->where('date', '>=', $start_date)
                ->where('date', '<=', $end_date);
            if ($client_type !== 'all') {
                $model->where('client_type', '=', (int)$client_type);
            }
            $data['sum_total'] = $model->first([
                DB::raw("COALESCE(sum((data->>'total_price')::float) ,0.00) as  sum_price"),
                DB::raw("COALESCE(sum((data->>'total_rebate')::float) ,0.00) as  sum_rebate"),
                DB::raw("COALESCE(sum((data->>'total_profit')::float) ,0.00) as  sum_profit")
            ]);
            $model->select([
                'date',
                'client_type',
                DB::raw("data->>'total_price' as total_price"),
                DB::raw("data->>'total_rebate' as total_rebate"),
                DB::raw("data->>'total_profit' as total_profit")
            ]);
            $data['recordsTotal'] = $data['recordsFiltered'] = $model->count();

            if ($export) {
                //导出逻辑
                $file_name = "设备盈亏日报表{$start_date}-{$end_date}.csv";
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
                        '设备',
                        '投注',
                        '返点',
                        '盈利',
                    ];
                    fputcsv($out, $columnNames);
                    $client_types = get_client_types();
                    $model->orderBy("date", 'desc')->chunk(500, function ($results) use (&$first, $out, $client_types) {
                        $datas = [];
                        foreach ($results as $item) {
                            $datas[] = [
                                $item->date,
                                $client_types[$item->client_type],
                                $item->total_price,
                                $item->total_rebate,
                                $item->total_profit
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
                $data['data'] = $model->orderBy("data->>{$columns[$order[0]['column']]['data']}", $order[0]['dir'])
                    ->skip($start)
                    ->take($length)
                    ->get();
                return $data;
            }
        }
    }
}

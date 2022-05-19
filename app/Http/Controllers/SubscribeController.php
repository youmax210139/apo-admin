<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Service\Models\Subscribe;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubscribeController extends Controller
{
    public function getIndex()
    {
        return view('subscribe.index');
    }

    public function postIndex(Request $request)
    {
        $export = (int)$request->get('export', 0);//导出Excel

        if ($request->ajax() || $export) {
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');

            $param['email'] = trim($request->get('email'));
            $param['created_start_date'] = $request->get('created_start_date');
            $param['created_end_date'] = $request->get('created_end_date');

            $where = array();

            if ($param['email']) {
                $where[] = ['email', '=', $param['email']];
            }
            if ($param['created_start_date']) {
                $where[] = ['created_at', '>=', $param['created_start_date']];
            }
            if ($param['created_end_date']) {
                $where[] = ['created_at', '<=', $param['created_end_date']];
            }

            $model = Subscribe::where($where);
            if (empty($export)) {
                $total = $model->count();

                $data['recordsTotal'] = $data['recordsFiltered'] = $total;

                $data['data'] = $model->skip($start)->take($length)
                    ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                    ->get();

                return response()->json($data);
            } else {
                //导出数据
                $file_name = "订阅记录.csv";
                $response = new StreamedResponse(null, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $file_name . '"',
                ]);
                $response->setCallback(function () use ($model) {
                    $out = fopen('php://output', 'w');
                    fwrite($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // 添加 BOM

                    $columnNames[] = 'ID';
                    $columnNames[] = '邮箱';
                    $columnNames[] = '订阅时间';
                    fputcsv($out, $columnNames);

                    $model->orderBy('id', 'DESC')->chunk(500, function ($results) use ($out) {
                        foreach ($results as $item) {
                            fputcsv($out, [
                                $item->id,
                                $item->email,
                                $item->created_at,
                            ]);
                        }
                    });
                    fclose($out);
                });
                $response->send();
            }
        }
    }
}

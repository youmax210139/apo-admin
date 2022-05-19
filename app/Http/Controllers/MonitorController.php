<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Service\Models\Monitor;
use Carbon\Carbon;

class MonitorController extends Controller
{
    public function getIndex()
    {
        $data['start_date'] = Carbon::now()->subDay();
        $data['end_date'] = Carbon::now();

        return view('monitor.index', $data);
    }

    public function postIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = [];
            $param = [];
            $param['start_date'] = (string)$request->get('start_date'); // 开始时间
            $param['end_date'] = (string)$request->get('end_date');     // 结束时间

            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');

            $where = function ($query) use ($param) {
                if (!empty($param['start_date'])) {
                    $query->where('monitor.created_at', '>=', $param['start_date']);
                }
                if (!empty($param['end_date'])) {
                    $query->where('monitor.created_at', '<', $param['end_date']);
                }
            };

            // 计算过滤后总数
            $count_query = Monitor::where($where);
            $data['recordsTotal'] = $data['recordsFiltered'] = $count_query->count();

            $data['data'] = Monitor::where($where)
                ->skip($start)->take($length)
                ->orderBy('monitor.created_at', 'desc')
                ->get([
                    'monitor.id',
                    'monitor.type',
                    'monitor.description',
                    'monitor.created_at'
                ]);

            return response()->json($data);
        }
    }

    public function getDetail(Request $request)
    {
        $id = (int)$request->get('id');

        $data = Monitor::where('monitor.id', $id)
            ->first([
                'monitor.id',
                'monitor.type',
                'monitor.description',
                'monitor.created_at'
            ]);

        return view('monitor.detail', $data);
    }
}

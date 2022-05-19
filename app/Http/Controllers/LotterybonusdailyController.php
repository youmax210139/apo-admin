<?php

namespace App\Http\Controllers;

use App\Http\Requests\LotteryBonusDailyRequest;
use Carbon\Carbon;
use Service\Models\ReportDailyLotteryBonus;

class LotterybonusdailyController extends Controller
{
    public function getIndex()
    {
        return view(
            'lottery-bonus-daily-report/index',
            [
                'start_date' => Carbon::yesterday()->toDateString(),
                'end_date' => Carbon::today()->toDateString(),
            ]
        );
    }

    public function postIndex(LotteryBonusDailyRequest $request)
    {
        $order_type = $request->get('order_type', 'asc');
        $page = (int)$request->get('start', 1);
        $length = (int)$request->get('length', 30);
        $start = $length * ($page - 1);
        $data = ReportDailyLotteryBonus::whereBetween('date', [$request->start_date, $request->end_date])
            ->skip($start)
            ->take($length)
            ->orderBy('date', $order_type)
            ->get();
        $rows = [];
        foreach ($data as $d) {
            $row = $d->data;
            $row['date'] = $d->date;
            $rows[] = $row;
        }
        $data = [];
        $data['data'] = $rows;
        $data['count'] = count($rows);
        return response()->json($data);
    }
}

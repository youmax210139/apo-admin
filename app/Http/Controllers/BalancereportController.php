<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Service\API\UserFundDate;

class BalancereportController extends Controller
{
    public function getIndex()
    {
        return view('report.balance-report', [
            'date' => Carbon::yesterday()->format("Y-m-d")
        ]);
    }

    public function postIndex(Request $request)
    {
        $params['date'] = $request->get('date', Carbon::yesterday());
        $params['frozen'] = (int)$request->get('frozen', -1);
        $params['show_zero'] = (int)$request->get('show_zero', 1);
        $params['username'] = trim($request->get('username'));
        $params['parent_id'] = (int)$request->get('parent_id', 0);
        $params['order_by'] = $request->get('order_by');
        $params['order_type'] = $request->get('order_type');

        $data = UserFundDate::getTeamUserFund($params);

        return response()->json($data);
    }
}

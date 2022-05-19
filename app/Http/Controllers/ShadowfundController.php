<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Service\Models\User;
use Service\Models\ShadowUserFund;
use Illuminate\Support\Facades\DB;

class ShadowfundController extends Controller
{
    public function getIndex(Request $request)
    {
        $username = $request->get('username', '');
        $user_id = (int)$request->get('user_id', 0);
        if (!empty($username)) {
            $username = User::where('username', $username)->value('username');
        } elseif ($user_id) {
            $username = User::where('id', $user_id)->value('username');
        }

        $start_time = $request->get('start_time', Carbon::today()->startOfDay()->toDateTimeString());//开始时间
        $end_time = $request->get('end_time', Carbon::today()->endOfDay()->toDateTimeString());//结束时间

        $data = [
            'username' => $username,
            'start_time' => $start_time,
            'end_time' => $end_time,
        ];

        return view('shadow-fund.index', $data);
    }

    /**
     * 账变列表数据
     *
     * @param OrderIndexRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postIndex(Request $request)
    {
        if ($request->ajax()) {
            $username = $request->get('username', '');
            $start_time = $request->get('start_time', Carbon::today()->startOfDay()->toDateTimeString());//开始时间
            $end_time = $request->get('end_time', Carbon::today()->endOfDay()->toDateTimeString());//结束时间
            $start_time = Carbon::parse($start_time)->toDateTimeString();
            $end_time = Carbon::parse($end_time)->toDateTimeString();

            $data = [];
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');
            $search = $request->get('search');

            if ($username) {
                $user = User::where('username', $username)->first(['id', 'username']);
                $user_id = $user->id ?? 0;
                $username = $user->username ?? '';
            } else {
                $user_id = 0;
                $username = '';
            }

            if ($user_id) {
                $data['data'] = ShadowUserFund::where('user_id', $user_id)
                    ->whereBetween('created_at', [$start_time, $end_time])
                    ->skip($start)
                    ->take($length)
                    ->orderBy('id', 'DESC')
                    ->get();
                foreach ($data['data'] as &$row) {
                    $row->username = $username;
                }
                $data['recordsTotal'] = ShadowUserFund::where('user_id', $user_id)
                    ->whereBetween('created_at', [$start_time, $end_time])
                    ->count();
                $data['recordsFiltered'] = $data['recordsTotal'];
                $data['statistics'] = ShadowUserFund::select([
                    'user_id',
                    DB::raw('SUM(CASE WHEN balance_diff > 0 THEN balance_diff ELSE 0 END) AS balance_diff_in'),
                    DB::raw('SUM(CASE WHEN balance_diff < 0 THEN balance_diff ELSE 0 END) AS balance_diff_out'),
                    DB::raw('SUM(CASE WHEN hold_balance_diff > 0 THEN hold_balance_diff ELSE 0 END) AS hold_balance_diff_in'),
                    DB::raw('SUM(CASE WHEN hold_balance_diff < 0 THEN hold_balance_diff ELSE 0 END) AS hold_balance_diff_out'),
                    DB::raw('SUM(CASE WHEN points_diff > 0 THEN points_diff ELSE 0 END) AS points_diff_in'),
                    DB::raw('SUM(CASE WHEN points_diff < 0 THEN points_diff ELSE 0 END) AS points_diff_out')
                ])->where('user_id', $user_id)
                    ->whereBetween('created_at', [$start_time, $end_time])
                    ->groupBy('user_id')
                    ->first();
            } else {
                $data['data'] = [];
                $data['recordsFiltered'] = $data['recordsTotal'] = 0;
                $data['statistics'] = [];
            }

            return response()->json($data);
        }
    }
}

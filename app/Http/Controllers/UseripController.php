<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use itbdw\Ip\IpLocation;
use Service\Models\UserLoginLog;
use App\Http\Requests\UserIpIndexRequest;

class UseripController extends Controller
{
    public function getIndex()
    {
        $data['start_date'] = Carbon::today()->subDays(15);
        $data['end_date'] = Carbon::today()->endOfDay();

        return view('user.ip', $data);
    }

    public function postIndex(UserIpIndexRequest $request)
    {
        if ($request->ajax()) {
            $data = array();
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');

            $param = array();
            $param['ip'] = (string)$request->get('ip', '');
            $param['start_date'] = $request->get('start_date', '');
            $param['end_date'] = $request->get('end_date', '');

            $subQuery = UserLoginLog::select([DB::raw('max(id) as id'), 'user_id', DB::raw('count(user_id) as total')])
                ->where(function ($query) use ($param) {
                    $query->where('ip', $param['ip']);
                    $query->where('created_at', '>=', $param['start_date']);
                    $query->where('created_at', '<=', $param['end_date']);
                })
                ->groupBy('user_id');
            $data['recordsTotal'] = $data['recordsFiltered'] = DB::table(DB::raw("({$subQuery->toSql()}) as ull1"))
                ->mergeBindings($subQuery->getQuery())
                ->leftJoin('user_login_log as ull', 'ull.id', 'ull1.id')->count();

            $data['data'] = DB::table(DB::raw("({$subQuery->toSql()}) as ull1"))
                ->mergeBindings($subQuery->getQuery())
                ->leftJoin('user_login_log as ull', 'ull.id', 'ull1.id')
                ->leftJoin('users as u', 'u.id', 'ull.user_id')
                ->leftJoin('users as tu', 'tu.id', 'u.top_id')
                ->leftJoin('user_fund as uf', 'uf.user_id', 'ull.user_id')
                ->orderBy('ull.created_at', 'desc')
                ->skip($start)->take($length)
                ->select(['ull.user_id', 'u.user_group_id', 'ull1.total', 'u.username', 'tu.username as top_username', 'ull.ip', 'uf.balance', 'ull.created_at'])
                ->get();
            foreach ($data['data'] as $key => $item) {
                if (false !== filter_var($item->ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                    $location = IpLocation::getLocation($item->ip);
                    $data['data'][$key]->ip = implode(" ", $location);
                }
            }
            return response()->json($data);
        }
    }
}

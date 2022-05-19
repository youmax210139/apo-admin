<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Service\Models\Lottery;

class LotteryreportController extends Controller
{
    public function getIndex(Request $request)
    {
        $data['start_date'] = $request->get('start_date', '');
        $data['end_date'] = $request->get('end_date', '');
        if (empty($data['start_date']) || empty($data['end_date'])) {
            $default_search_time = get_config('default_search_time', 0);
            $data = [
                'start_date' => Carbon::now()->hour >= $default_search_time ?
                    Carbon::today()->addHours($default_search_time) :
                    Carbon::yesterday()->addHours($default_search_time),
                'end_date' => Carbon::now()->hour >= $default_search_time ?
                    Carbon::tomorrow()->addHours($default_search_time)->subSecond(1) :
                    Carbon::today()->addHours($default_search_time)->subSecond(1),
            ];
        }
        $data['type'] = $request->get('reporttype', 'win');
        $data['lottery_list'] = Lottery::all();
        $data['lottery_id'] = (int)$request->get('lottery_id', 0);
        $data['searchtype'] = (int)$request->get('searchtype', 0);
        $data['num'] = $request->get('num', 20);
        $data['results'] = [];
        $where_sql = '';
        if ($data['lottery_id']) {
            $where_sql = "lottery_id={$data['lottery_id']} AND ";
        }
        if ($data['type'] == 'win') {
            $order_by = 'total_bonus DESC';
            if ($data['searchtype'] == 1) {
                $order_by = 'total_price DESC';
            } elseif ($data['searchtype'] == 2) {
                $order_by = 'total_win DESC';
            } elseif ($data['searchtype'] == 3) {
                $order_by = 'total_win ASC';
            }

            $data['results'] = DB::select('
                SELECT
                    T1.*,
                    users.username,
                    users.created_at,
                    total_price - total_rebate - total_bonus  AS total_win,
                    (total_price - total_rebate - total_bonus) / nullif(total_price, 0) AS total_win_rate,
                    user_profile.value as user_observe
                FROM (
                    SELECT
                        "user_id",
                        SUM( price ) AS total_price,
                        SUM( rebate ) AS total_rebate,
                        SUM( bonus ) AS total_bonus
                    FROM
                        "report_lottery"
                    WHERE ' . $where_sql . ' created_at >= ? AND created_at <= ? AND price >= 0
                    GROUP BY
                        "user_id"
                ) AS T1
                LEFT JOIN "users" ON "users"."id" = T1."user_id"
                LEFT JOIN "user_profile" ON "user_profile"."user_id" = T1."user_id" AND "user_profile"."attribute" = ?
                WHERE users.user_group_id = 1
                ORDER BY ' . $order_by . ' LIMIT ?', [$data['start_date'], $data['end_date'], "user_observe", $data['num']]
            );
        } elseif ($data['type'] == 'user') {
            if ($data['searchtype'] == 0) {
                $data['results'] = DB::select('
                    SELECT
                        t1.*,
                        lottery.name as lottery_name
                    FROM (
                        SELECT
                            lottery_id,
                            issue,
                            count( DISTINCT user_id ) AS total_num
                        FROM
                            "report_lottery"
                        WHERE ' . $where_sql . '
                            created_at >=?
                            AND created_at <= ?
                            AND price >= 0
                        GROUP BY
                            lottery_id,
                            "issue"
                        ORDER BY
                            total_num DESC
                            LIMIT ?
                    ) t1
                    LEFT JOIN lottery ON lottery.id = lottery_id
                    ORDER BY
                        total_num DESC', [$data['start_date'], $data['end_date'], $data['num']]);
            } elseif ($data['searchtype'] == 1) {
                $data['results'] = DB::select('
                    SELECT
                        t1.*,
                        lottery.name as lottery_name
                    FROM (
                        SELECT
                            lottery_id,
                            count( DISTINCT user_id) AS total_num
                        FROM
                            "report_lottery"
                        WHERE ' . $where_sql . '
                            created_at >=?
                            AND created_at <= ?
                            AND price >= 0
                        GROUP BY
                            lottery_id
                        ORDER BY
                            total_num DESC
                            LIMIT ?
                    ) t1
                    LEFT JOIN lottery ON lottery.id = lottery_id
                    ORDER BY
                        total_num DESC', [$data['start_date'], $data['end_date'], $data['num']]
                );
            } elseif ($data['searchtype'] == 2) {
                $data['results'] = DB::select('
                    SELECT
                        t1.*,
                        lottery.NAME AS lottery_name,
                        concat( m2.NAME, \' - \', m1.NAME ) AS method_name
                    FROM (
                        SELECT
                            lottery_id,
                            lottery_method_id,
                            count( DISTINCT user_id ) AS total_num
                        FROM
                            "report_lottery"
                        WHERE ' . $where_sql . '
                            created_at >= ?
                            AND created_at <= ?
                            AND price >= 0
                        GROUP BY
                            lottery_id,
                            lottery_method_id
                        ORDER BY
                            total_num DESC
                            LIMIT ?
                    ) t1
                    LEFT JOIN lottery ON lottery.id = lottery_id
                    LEFT JOIN lottery_method m1 ON m1.id = lottery_method_id
                    LEFT JOIN lottery_method m2 ON m2.id = m1.parent_id
                    ORDER BY
                    total_num DESC', [$data['start_date'], $data['end_date'], $data['num']]
                );
            } elseif ($data['searchtype'] == 3) {
                $data['results'] = DB::select('
                    SELECT
                        t1.*,
                        lottery.name as lottery_name
                    FROM (
                        SELECT
                            lottery_id,
                            issue,
                            SUM( price ) AS total_price
                        FROM
                            "report_lottery"
                        WHERE ' . $where_sql . '
                            created_at >=?
                            AND created_at <= ?
                            AND price >= 0
                        GROUP BY
                            lottery_id,
                            "issue"
                        ORDER BY
                            total_price DESC
                            LIMIT ?
                    ) t1
                    LEFT JOIN lottery ON lottery.id = lottery_id
                    ORDER BY
                        total_price DESC', [$data['start_date'], $data['end_date'], $data['num']]
                );
            }
        } elseif ($data['type'] == 'profit') {
            $order_by = 'total_win ASC';
            if ($data['searchtype'] == 1) {
                $order_by = 'total_win DESC';
            }
            $data['results'] = DB::select('
                SELECT
                    t1.*,
                    lottery.name as lottery_name
                FROM (
                    SELECT
                        lottery_id,
                        issue,
                        SUM( price ) AS total_price,
                        SUM( rebate ) AS total_rebate,
                        SUM( bonus ) AS total_bonus,
                        (SUM( price ) -  SUM( rebate ) -  SUM( bonus )) AS total_win
                    FROM
                        "report_lottery"
                    LEFT JOIN "users" ON "users"."id" = report_lottery."user_id"
                    WHERE ' . $where_sql . '
                        users.user_group_id = 1 AND
                        report_lottery.created_at >=?
                        AND report_lottery.created_at <= ?
                    GROUP BY
                        lottery_id,
                        "issue"
                    ORDER BY
                        ' . $order_by . '
                        LIMIT ?
                ) t1
                LEFT JOIN lottery ON lottery.id = lottery_id
                ORDER BY
                    ' . $order_by, [$data['start_date'], $data['end_date'], $data['num']]
            );
        }
        return view('lottery-report.index', $data);
    }

    public function postIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = [];

            return response()->json($data);
        }
    }
}

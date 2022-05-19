<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Service\API\GoogleAuthenticator;
use Service\Events\Activity;
use Service\Models\Coupon;
use Service\Models\CouponSplit;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{
    public function getIndex()
    {
        return view('coupon.index');
    }

    public function postIndex(Request $request)
    {
        if ($request->ajax()) {
            $data = array();
            $data['draw'] = $request->get('draw');
            $start = $request->get('start');
            $length = $request->get('length');
            $order = $request->get('order');
            $columns = $request->get('columns');
            $start_date = $request->get('start_date', '');
            $end_date = $request->get('end_date', '');
            $data['recordsTotal'] = Coupon::where(function ($query) use ($start_date, $end_date) {
                if ($start_date) {
                    $query->where('created_at', '>=', $start_date);
                }
                if ($start_date) {
                    $query->where('created_at', '<=', $end_date);
                }
            })->count();

            $data['recordsFiltered'] = $data['recordsTotal'];
            $data['data'] = Coupon::where(function ($query) use ($start_date, $end_date) {
                if ($start_date) {
                    $query->where('created_at', '>=', $start_date);
                }
                if ($start_date) {
                    $query->where('created_at', '<=', $end_date);
                }
            })
                ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                ->skip($start)->take($length)
                ->get();

            return response()->json($data);
        }
    }

    /**
     * 详情
     *
     * @return \Illuminate\Http\Response
     */
    public function getDetail(Request $request)
    {
        $id = $request->get('id', 0);
        $data['coupons'] = CouponSplit::select([
            'coupon_split.*',
            'users.username AS collect_username'
        ])
            ->leftJoin('users', 'users.id', 'coupon_split.collect_id')
            ->where('coupon_id', $id)->orderBy('updated_at', 'desc')->get();
        return view('coupon.detail', $data);
    }

    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function getSend()
    {
        return view('coupon.send');
    }

    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function postSend(Request $request)
    {
        $amount = (float)$request->get('amount', 0);
        $num = (int)$request->get('num', 0);
        $content = $request->get('content', '');
        if ($amount <= 0) {
            return response()->json([
                'status' => 1,
                'msg' => '红包总金额不能小于等于0'
            ]);
        }

        if ($num <= 0) {
            return response()->json([
                'status' => 1,
                'msg' => '红包个数不能小于等于0'
            ]);
        }
        $code = $request->get('google_code', '');
        if (!$code) {
            return response()->json([
                'status' => 1,
                'msg' => '请输入谷歌动态验证码'
            ]);
        }
        if (empty(auth()->user()->google_key)) {
            return response()->json([
                'status' => 1,
                'msg' => '先请绑定谷歌登录器才能操作！'
            ]);
        }


        $g = new GoogleAuthenticator();
        if (!$g->verifyCode(auth()->user()->google_key, $code)) {
            return response()->json([
                'status' => 1,
                'msg' => '谷歌动态验证码错误！'
            ]);
        }
        if (\Service\API\Coupon::adminSend($amount, $num, $content)) {
            return response()->json([
                'status' => 0,
                'msg' => '红包发送成功！'
            ]);
        }
        return response()->json([
            'status' => 1,
            'msg' => '发送失败！'
        ]);
    }

    public function getConfig()
    {
        $coupon_setting = Redis::get('hourly_coupon_setting');
        $coupon_setting = json_decode($coupon_setting, true);
        if (!$coupon_setting || empty($coupon_setting['setting'])) {
            $coupon_setting['enabled'] = 0;
            $coupon_setting['setting'] = [];
            for ($i = 0; $i <= 23; $i++) {
                $coupon_setting['setting'][] = [
                    'title' => $i . '点',
                    'amount' => 0,
                    'num' => 0
                ];
            }
        }
        return view('coupon.config', $coupon_setting);
    }

    public function postConfig(Request $request)
    {
        $enabled = (int)$request->get('enabled', 0);
        $setting = $request->get('setting');
        $config['title'] = $request->get('title', '');
        $config['content'] = $request->get('content', '');
        $config['enabled'] = $enabled;
        $config['setting'] = $setting;
        Redis::set('hourly_coupon_setting', json_encode($config));
        return response()->json([
            'status' => 0,
            'msg' => '保存成功！'
        ]);
    }

    public function postPush(Request $request)
    {
        $id = $request->get('id', 0);
        $coupon = Coupon::find($id);
        if (!$coupon) {
            return response()->json([
                'status' => 1,
                'msg' => '找不到红包信息！'
            ]);
        }
        //推送消息
        Event(new Activity([
            'type' => 'coupon',
            'id' => id_encode($coupon->id),
            'username' => '系统',
            'title' => $coupon->title,
            'content' => $coupon->content,
        ]));
        return response()->json([
            'status' => 0,
            'msg' => '推送成功！'
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class RegisterurlController extends Controller
{
    public function getIndex()
    {
        return view('registerurl.index');
    }

    public function postIndex(Request $request)
    {
        $data = array();
        $data['draw'] = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $columns = $request->get('columns');
        $order = $request->get('order');
        $search = $request->get('search');
        if ($request->get('username')) {
            $search['value'] = $request->get('username');
        }

        if (strlen($search['value']) > 0) {
            $data['recordsFiltered'] = \Service\Models\RegisterCodes::leftJoin('users', 'users.id', 'register_codes.user_id')
                ->where(function ($query) use ($search) {
                    $query->where('users.username', 'LIKE', '%' . $search['value'] . '%')
                        ->orWhere('code', 'LIKE', '%' . $search['value'] . '%');
                })->count();

            $data['data'] = \Service\Models\RegisterCodes::where(function ($query) use ($search) {
                $query->where('users.username', 'LIKE', '%' . $search['value'] . '%')
                    ->orWhere('code', 'LIKE', '%' . $search['value'] . '%');
            })->leftJoin('users', 'users.id', 'register_codes.user_id')
                ->skip($start)->take($length)
                ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                ->orderBy('register_codes.id', 'DESC')
                ->skip($start)->take($length)
                ->get(['register_codes.*',
                    'users.username'
                ]);
        } else {
            $data['recordsTotal'] = \Service\Models\RegisterCodes::count();
            $data['recordsFiltered'] = $data['recordsTotal'];
            $data['data'] = \Service\Models\RegisterCodes::leftJoin('users', 'register_codes.user_id', 'users.id')
                ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                ->orderBy('register_codes.id', 'DESC')
                ->skip($start)->take($length)
                ->get(['register_codes.*',
                    'users.username'
                ]);
        }
        foreach ($data['data'] as &$v) {
            if ($v->expired) {
                $expired_at = (new Carbon($v->created_at))->addDays($v->expired);
                if ($expired_at < now()) {
                    $v->expired = '已过期';
                } else {
                    $v->expired = (string)$expired_at;
                }
            } else {
                $v->expired = "永久";
            }
            $v->rebates = json_decode($v->rebates);
            foreach ($v->rebates as $k => $v1) {
                $v->rebates->$k = array('title' => $v1->name, 'value' => ($v1->value) . "%");
            }
        }
        return response()->json($data);
    }

    public function deleteDel(Request $request)
    {
        $id = (int)$request->get('id', 0);
        \Service\Models\RegisterCodes::find($id)->delete();
        return redirect('/registerurl\/')->withSuccess('删除成功');
    }
}

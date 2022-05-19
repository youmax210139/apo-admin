<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Service\Models\Guestbook;

class GuestbookController extends Controller
{
    private $fields = [
        'status' => 0,
        'remark' => '',
    ];

    public function getIndex()
    {
        return view('guestbook.index');
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
            $title = $request->get('title');
            $start_time = $request->get('created_start_date');
            $end_time = $request->get('created_end_date');
            $query = Guestbook::query();
            if ($title) {
                $query->where('title', $title);
            }
            if ($start_time) {
                $query->where('created_at', '>=', $start_time);
            }
            if ($end_time) {
                $query->where('created_at', '<=', $end_time);
            }

            $data['recordsTotal'] = $data['recordsFiltered'] = $query->count();

            $data['data'] = $query
                ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                ->skip($start)->take($length)
                ->get();

            return response()->json($data);
        }
    }

    /**
     * 编辑页面
     *
     * @return \Illuminate\Http\Response
     */
    public function getEdit(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $guestbook = Guestbook::find($id);
        if (!$guestbook) {
            return redirect('/guestbook\/')->withErrors("找不到该留言");
        }

        return view('guestbook.edit', $guestbook);
    }

    public function getDetail(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $guestbook = Guestbook::find($id);
        if (!$guestbook) {
            return redirect('/guestbook\/')->withErrors("找不到该留言");
        }

        //状态
        switch ($guestbook->status) {
            case "1":
                $guestbook->status = '不需处理';
                break;
            case "2":
                $guestbook->status = '处理完成';
                break;
            default:
                $guestbook->status = '待处理';
        }

        return view('guestbook.detail', $guestbook);
    }

    /**
     * 处理留言
     *
     * @param Request $request
     * @return mixed
     */
    public function putEdit(Request $request)
    {
        $id = (int)$request->get('id', 0);
        $guestbook = Guestbook::find($id);
        if (!$guestbook) {
            return redirect('/guestbook\/')->withErrors("找不到该留言");
        }

        foreach (array_keys($this->fields) as $field) {
            $guestbook->$field = $request->get($field, $this->fields[$field]);
        }

        if ($guestbook->save()) {
            return redirect('/guestbook\/')->withSuccess('处理成功');
        } else {
            return redirect('/guestbook\/')->withErrors('处理失败');
        }
    }
}

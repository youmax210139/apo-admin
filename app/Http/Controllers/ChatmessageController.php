<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Service\Models\ChatMessage;

class ChatmessageController extends Controller
{
    protected $fields = [
        'subject' => '',
        'published_at' => '',
        'end_at' => '',
        'content' => '',
        'sort' => 0,
        'is_top' => 0
    ];

    public function getIndex()
    {
        //删除过期消息
        ChatMessage::where('created_at', '<=', Carbon::today()->subDays(get_config('massage_expired_day', 120)))->delete();
        return view('chat-messages.index');
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
            $search = $request->get('search');
            $sender = $request->get('sender', '');
            $receiver = $request->get('receiver', '');
            $start_date = $request->get('start_date', '');
            $end_date = $request->get('end_date', '');
            $message = trim($request->get('message', ''));
            $data['recordsTotal'] = \Service\Models\ChatMessage::withTrashed()->leftJoin('users AS su', 'su.id', 'chat_message.from_user_id')
                ->leftJoin('users AS ru', 'ru.id', 'chat_message.to_user_id')
                ->where(function ($query) use ($sender, $receiver, $start_date, $end_date, $message) {
                    if ($sender) {
                        $query->where('su.username', $sender);
                    }
                    if ($receiver) {
                        $query->where('ru.username', $receiver);
                    }
                    if ($start_date && strtotime($start_date)) {
                        $query->where('chat_message.created_at', '>=', $start_date);
                    }
                    if ($end_date && strtotime($end_date)) {
                        $query->where('chat_message.created_at', '<=', $end_date);
                    }
                    if ($message) {
                        $query->where('chat_message.message', 'LIKE', '%' . $message . '%');
                    }
                })->count();

            $data['recordsFiltered'] = $data['recordsTotal'];
            $data['data'] = \Service\Models\ChatMessage::withTrashed()->leftJoin('users AS su', 'su.id', 'chat_message.from_user_id')
                ->leftJoin('users AS ru', 'ru.id', 'chat_message.to_user_id')
                ->where(function ($query) use ($sender, $receiver, $start_date, $end_date, $message) {
                    if ($sender) {
                        $query->where('su.username', $sender);
                    }
                    if ($receiver) {
                        $query->where('ru.username', $receiver);
                    }
                    if ($start_date && strtotime($start_date)) {
                        $query->where('chat_message.created_at', '>=', $start_date);
                    }
                    if ($end_date && strtotime($end_date)) {
                        $query->where('chat_message.created_at', '<=', $end_date);
                    }
                    if ($message) {
                        $query->where('chat_message.message', 'LIKE', '%' . $message . '%');
                    }
                })
                ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                ->skip($start)->take($length)
                ->get([
                    'chat_message.id',
                    'chat_message.message',
                    'chat_message.created_at',
                    'chat_message.deleted_at',
                    'su.username AS sender',
                    'ru.username AS receiver'
                ]);

            return response()->json($data);
        }
    }

    //批量软删除
    public function getDelete(Request $request)
    {
        $ids = $request->get('ids', '');
        if (empty($ids)) {
            return redirect()->back()->withErrors("删除失败，请选择记录");
        }
        preg_match_all("/(\d+)/", $ids, $match_ids);

        $record_count = ChatMessage::whereIn('id', $match_ids[1])->delete();
        return redirect()->back()->withSuccess("成功删除 {$record_count} 条记录");
    }
}

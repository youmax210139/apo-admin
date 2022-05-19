<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Service\Models\Messages;
use Service\Models\UserMessage;

class MessageController extends Controller
{
    public function getIndex()
    {
        $expired_day = Carbon::today()->subDays(get_config('message_expired_day', 60));

        //删除过期消息
        UserMessage::leftJoin('messages', 'messages.id', 'user_messages.message_id')
            ->where('messages.send_at', '<=', $expired_day)->delete();
        Messages::where('send_at', '<=', $expired_day)->delete();

        return view('messages.index');
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
            $sender = $request->get('sender', '');
            $receiver = $request->get('receiver', '');
            $start_date = $request->get('start_date', '');
            $end_date = $request->get('end_date', '');

            $data['recordsTotal'] = \Service\Models\UserMessage::leftJoin('messages', 'messages.id', 'user_messages.message_id')
                ->leftJoin('users AS su', 'su.id', 'user_messages.sender_id')
                ->leftJoin('users AS ru', 'ru.id', 'user_messages.receiver_id')
                ->where('sender_type', 0)
                ->where(function ($query) use ($sender, $receiver, $start_date, $end_date) {
                    if ($sender) {
                        $query->where('su.username', $sender);
                    }
                    if ($receiver) {
                        $query->where('ru.id', $receiver);
                    }
                    if ($start_date && strtotime($start_date)) {
                        $query->where('messages.send_at', '>=', $start_date);
                    }
                    if ($end_date && strtotime($end_date)) {
                        $query->where('messages.send_at', '<=', $end_date);
                    }
                })->count();

            $data['recordsFiltered'] = $data['recordsTotal'];
            $data['data'] = \Service\Models\UserMessage::leftJoin('messages', 'messages.id', 'user_messages.message_id')
                ->leftJoin('users as su', 'su.id', 'user_messages.sender_id')
                ->leftJoin('users as ru', 'ru.id', 'user_messages.receiver_id')
                ->where('sender_type', 0)
                ->where(function ($query) use ($sender, $receiver, $start_date, $end_date) {
                    if ($sender) {
                        $query->where('su.username', $sender);
                    }
                    if ($receiver) {
                        $query->where('ru.id', $receiver);
                    }
                    if ($start_date && strtotime($start_date)) {
                        $query->where('messages.send_at', '>=', $start_date);
                    }
                    if ($end_date && strtotime($end_date)) {
                        $query->where('messages.send_at', '<=', $end_date);
                    }
                })
                ->orderBy($columns[$order[0]['column']]['data'], $order[0]['dir'])
                ->orderBy('id', 'DESC')
                ->skip($start)->take($length)
                ->get([
                    'messages.subject',
                    'messages.content',
                    'messages.send_at',
                    'user_messages.*',
                    'su.username AS sender',
                    'ru.username AS receiver'
                ]);

            foreach ($data['data'] as &$notice) {
                if (mb_strlen($notice->subject) > 12) {
                    $notice->subject = mb_substr($notice->subject, 0, 100) . '...';
                }
            }

            return response()->json($data);
        }
    }
}

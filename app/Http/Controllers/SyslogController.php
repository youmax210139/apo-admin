<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;

class SyslogController extends Controller
{
    public function getIndex(Request $request)
    {
        $path = $request->get('path', '');
        $clear_long = $request->get('clear_long', '');
        $data['directories'] = Storage::disk('logs')->directories($path);
        $data['files'] = Storage::disk('logs')->files($path);
        if ($clear_long) {
            $long = Redis::keys('long:*');
            foreach ($long as $key) {
                Redis::set($key, '');
            }
        }
        return view('sys-log.index', $data);
    }

    public function getDownload(Request $request)
    {
        $flag = $request->get('flag', '');
        $file_name = $request->get('file', '');
        if (!$file_name || !Storage::disk('logs')->exists($file_name)) {
            abort(404);
        }
        if ($flag && Storage::disk('logs')->size($file_name) <= 6 * 1204 * 1024) {
            $data['name'] = $file_name;
            $data['content'] = Storage::disk('logs')->get($file_name);
            return view('sys-log.view', $data);
        }
        return response()->stream(function () use ($file_name) {
            $stream = Storage::disk('logs')->readStream($file_name);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Type' => Storage::disk('logs')->mimeType($file_name),
            'Content-Length' => Storage::disk('logs')->size($file_name),
            'Content-Disposition' => 'attachment; filename="' . basename($file_name) . '"',
            'Pragma' => 'public',
        ]);
    }
}

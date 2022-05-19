<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function getIndex()
    {
        $client = new \GuzzleHttp\Client();
        $url = get_config('remote_pic_url') . '/upload/list';
        $time = time();
        $key = get_config('remote_pic_key');
        $lists = [];
        try {
            $response = $client->request('GET', $url, [
                'query' => [
                    'platform' => get_config('app_ident'),
                    'time' => $time,
                    'signature' => md5(get_config('app_ident') . $time . $key),
                ]
            ]);
            $lists = json_decode($response->getBody()->getContents());
        } catch (\Exception $e) {
        }
        return view('config.upload', ['data' => $lists, 'url' => get_config('remote_pic_url')]);
    }

    public function postIndex(Request $request)
    {
        $file = $request->file('pic');//获取文件

        $validator = Validator::make($request->all(), [
            'pic' => 'required|image'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors("图片格式不正确");
        }
        if ($file->getClientSize() > 2 * 1024 * 1024) {
            return redirect()->back()->withErrors("图片不能大于2m");
        }
        $client = new \GuzzleHttp\Client();
        $url = get_config('remote_pic_url') . '/upload';
        $time = time();
        $key = get_config('remote_pic_key');
        $array = [
            'form_params' => [
                'platform' => 'common/' . get_config('app_ident', 'jc'),
                'time' => $time,
                'ext' => $file->guessClientExtension(),
                'signature' => md5('common/' . get_config('app_ident', 'jc') . $time . $key),
                'file' => File::get($file)
            ]];
        try {
            $response = $client->request('post', $url, $array);
            $img_path = $response->getBody()->getContents();
            return redirect()->back()->withSuccess("图片上传成功，请复制地址" . $img_path);
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors('对不起，消息发送失败，请联系客服！' . $exception->getMessage());
        }
    }

    public function getDelpic(Request $request)
    {
        $pic_name = $request->get('pic_name');
        $client = new \GuzzleHttp\Client();
        $url = get_config('remote_pic_url') . '/upload/del';
        $time = time();
        $key = get_config('remote_pic_key');
        $array = [
            'form_params' => [
                'platform' => 'common/' . get_config('app_ident', 'jc'),
                'time' => $time,
                'signature' => md5('common/' . get_config('app_ident', 'jc') . $time . $key),
                'filename' => $pic_name
            ]];
        try {
            $client->request('post', $url, $array);
            return redirect('upload')->withSuccess("操作成功");
        } catch (\Exception $exception) {
            return redirect()->back()->withErrors('对不起，操作失败！' . $exception->getMessage());
        }
    }
}

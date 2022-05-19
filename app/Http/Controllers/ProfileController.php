<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Service\API\GoogleAuthenticator;
use Service\Models\Admin\AdminUser;
use App\Http\Requests\PasswordUpdateRequest;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function getPassword()
    {
        return view('profile.password');
    }

    public function putPassword(PasswordUpdateRequest $request)
    {
        $user = AdminUser::find(Auth()->id());

        if ($user) {
            $old_password = $request->get('old_password');

            if (!Hash::check($old_password, $user->password)) {
                return redirect()->back()->withErrors("原密码输入不正确");
            }

            $user->password = bcrypt($request->get('new_password'));

            $user->save();

            return redirect()->back()->withSuccess("密码修改成功");
        }
        return redirect()->back()->withErrors("密码修改失败");
    }

    public function getGooglekey(Request $request)
    {
        $user = auth()->user();
        $data['google_key'] = '';
        if (empty($user->google_key)) {
            $g = new GoogleAuthenticator();
            $secret = $g->createSecret();
            $request->session()->put('google_secret', $secret);

            $googlekey = 'otpauth://totp/apadm-' . $user->username . '-' . get_config('app_ident', 'local') . '?secret=' . $secret;
            $data['google_key'] = $googlekey;
        }
        return view('profile.googlekey', $data);
    }

    public function putGooglekey(Request $request)
    {
        $user = auth()->user();
        $flag = $request->get('flag');
        if ($flag == 'unbind') {
            $code = $request->get('code', '');
            $google_secret = $user->google_key;
            if ($google_secret) {
                $g = new GoogleAuthenticator();
                if (!$g->verifyCode($google_secret, $code)) {
                    return redirect()->back()->withErrors("动态验证码错误");
                }
                $request->session()->forget('google_secret');
                $user->google_key = '';
            } else {
                return redirect()->back()->withErrors("请输入动态验证码");
            }
        } else {
            $code = $request->get('code', '');
            $google_secret = $request->session()->get('google_secret');
            if ($google_secret) {
                $g = new GoogleAuthenticator();
                if (!$g->verifyCode($google_secret, $code)) {
                    return redirect()->back()->withErrors("动态验证码错误");
                }
                $request->session()->forget('google_secret');
                $user->google_key = $google_secret;
            } else {
                return redirect()->back()->withErrors("请输入动态验证码");
            }
        }
        $user->save();
        return redirect()->back()->withSuccess("绑定谷歌登录器成功");
    }
}

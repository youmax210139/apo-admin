<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Service\API\GoogleAuthenticator;
use Service\Models\Admin\AdminUser;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        login as _login;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }
    protected function validateLogin(\Illuminate\Http\Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required',
            'password' => 'required',
            'code' => 'required',
            'captcha' => 'required|captcha',
        ]);
    }
    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(\Illuminate\Http\Request $request)
    {
        $user = AdminUser::select(['google_key', 'is_locked'])
            ->where('username', $request->get('username'))
            ->first();
        if (!$user) {
            return response()->redirectTo("/login")->withErrors("用户不存在");
        }
        if ($user->is_locked) {
            return response()->redirectTo("/login")->withErrors("账号已冻结，请联系客服！");
        }
        $code = $request->get('code', '');
        if (!empty($user->google_key)) {
            if (empty($code)) {
                return response()->redirectTo("/login")->withErrors("动态验证码错误！");
            } else {
                $g = new GoogleAuthenticator();
                if (!$g->verifyCode($user->google_key, $code)) {
                    return response()->redirectTo("/login")->withErrors("动态验证码错误！");
                }
            }
        }

        return $this->_login($request);
    }
}

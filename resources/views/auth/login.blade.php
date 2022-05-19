@include('layouts.baseHeader')
<div class="container">
    <div class="row vertical-center">
        <div class="col-md-8 col-md-offset-2">
            @include('partials.errors')
            @include('partials.success')
            <div class="panel panel-primary">
                <div class="panel-heading">办公自动化系统登录</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="/login">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                            <label for="username" class="col-md-4 control-label">帐号</label>
                            <div class="col-md-6">
                                <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" maxlength="20" autocomplete="off" required>
                                @if ($errors->has('username'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">密码</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('code') ? ' has-error' : '' }}">
                            <label for="code" class="col-md-4 control-label">动态验证码</label>

                            <div class="col-md-6">
                                <input id="code" placeholder="谷歌动态验证码" type="text" class="form-control" name="code" autocomplete="off" required>
                                @if ($errors->has('code'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('code') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('captcha') ? ' has-error' : '' }}">
                            <label for="captcha" class="col-md-4 control-label">验证码</label>
                            <div class="col-md-4">
                                <input class="form-control" name="captcha" autocomplete="off" required>
                                @if ($errors->has('captcha'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('captcha') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col col-md-2" style="padding-left: 0" id="captcha_div">
                                {!! captcha_img() !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    登录
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@section('js')
    <script type="text/javascript">
        if (self != top) {
            top.window.location.reload();
        }
        $(document).ready(function () {

            $("#captcha_div").children('img').css('cursor', 'pointer');
            $("#captcha_div").children('img').click(function () {
                var old = $(this).attr('src');
                $(this).attr('src', old.substr(0, old.indexOf('?') + 1) + Math.random());
            });
        });
    </script>
@stop
@include('layouts.baseFooter')

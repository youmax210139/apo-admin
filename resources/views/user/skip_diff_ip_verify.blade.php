<form id="skip_diff_ip_verify-form" method="post">
    <table class="table table-hover table-striped">
        <tbody>
        <tr>
            <td class="text-right" width="190">系统配置状态</td>
            <td class="text-left">
                <b>{{get_config('deff_login_ip_verify',0)?'验证':'不验证'}}</b>
            </td>
        </tr>
        <tr>
            <td class="text-right" width="190">用户名</td>
            <td class="text-left">
                <b>{{$username}}</b>
            </td>
        </tr>
        <tr>
            <td class="text-right">当前验证状态</td>
            <td class="text-left">
                @if($value)
                    <span class="text-danger">跳过</span>
                @else
                    <span class="text-success">不跳过</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="text-right">设置</td>
            <td class="text-left">
                <input type="hidden" name="user_id" value="{{$id}}">
                <div>
                    <label>
                        <input type="radio" name="type" value="0" checked>
                        @if($value)
                            取消此用户跳过验证
                        @else
                            此用户跳过验证
                        @endif
                    </label>
                </div>
                <div>
                    <label>
                        <input type="radio" name="type" value="1">
                        @if($value)
                            取消此用户以及其下级所有用户跳过验证
                        @else
                            此用户以及其下级所有用户跳过验证
                        @endif
                    </label>
                </div>

            </td>
        </tr>
        @if($value)
        <tr>
            <td class="text-right">上次操作来源</td>
            <td class="text-left">
                <span class="text-right">{{$value}}</span>
            </td>
        </tr>
        @endif
        </tbody>
    </table>
    <div class="alert alert-warning alert-dismissible">
        提示！此设置项只有在配置验证状态才生效.
    </div>
</form>
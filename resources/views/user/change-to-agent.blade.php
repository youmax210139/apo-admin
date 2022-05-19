<form id="change-to-agent-form" method="post">
    <table class="table table-hover table-striped">
        <tbody>
        <tr>
            <td class="text-right" width="190">用户当前类型</td>
            <td class="text-left">
                <b>{{$user_type->name}}</b>
            </td>
        </tr>
        <tr>
            <td class="text-right" width="190">用户名</td>
            <td class="text-left">
                <b>{{$user->username}}</b>
            </td>
        </tr>
        <tr>
            <td class="text-right">设置</td>
            <td class="text-left">
                <input type="hidden" name="user_id" value="{{$user->id}}">
                <div>
                    <label>
                        <input type="radio" name="user_type" value="2" checked>
                        代理
                    </label>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</form>
<div style="background-color: #F19B2C;line-height: 3;color:#FCEEDE">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp提示！此设置变更后无法再恢复先前身份</div>

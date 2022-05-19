<form id="private-return-form" method="post">
    <table class="table table-hover table-striped">
        <tbody>
        <tr>
            <td class="text-right" width="190">用户名</td>
            <td class="text-left">
                <b>{{$username}}</b>
            </td>
        </tr>
        <tr>
            <td class="text-right">当前私返状态</td>
            <td class="text-left">
                @if(!$value)
                    <span class="text-danger">禁止</span>
                @else
                    <span class="text-success">允许</span>
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
                        @if(!$value)
                            允许此用户私返
                        @else
                            禁止此用户私返
                        @endif
                    </label>
                </div>
                <div>
                    <label>
                        <input type="radio" name="type" value="1">
                        @if(!$value)
                            允许此用户以及其下级所有用户私返
                        @else
                            禁止此用户以及其下级所有用户私返
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
</form>

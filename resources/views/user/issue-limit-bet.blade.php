<form id="issuelimitbet-form" method="post">
    <input type="hidden" name="id" value="{{$id}}" >
    <table class="table table-hover table-striped">
        <tbody>
        <tr>
            <td class="text-right" width="190">用户名</td>
            <td class="text-left">
                <b>{{$username}}</b>
            </td>
        </tr>
        <tr>
            <td class="text-right">限额</td>
            <td class="text-left">
                        <input type="text" value="{{$value}}" name="value" class="form-control" placeholder="请填写大于0的数，留空为不限制" maxlength="64">
                </div>
            </td>
        </tr>
        </tbody>
    </table>
</form>
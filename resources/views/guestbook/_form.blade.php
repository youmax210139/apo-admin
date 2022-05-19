<div class="form-group">
    <table class="table table-striped table-bordered table-hover">
        <tbody>
        <tr>
            <td>信箱</td>
            <td>{{$email}}</td>
            <td>称呼</td>
            <td>{{$appellation}}</td>
        </tr>
        <tr>
            <td>通讯软件</td>
            <td>{{$app_name}}</td>
            <td>通讯软件帐号</td>
            <td>{{$app_account}}</td>
        </tr>
        <tr>
            <td>主题</td>
            <td>{{$title}}</td>
            <td>状态</td>
            <td>
                <select name="status" class="form-control" style="width: 200px;">
                    <option value="0" @if ($status == 0) selected @endif>待处理</option>
                    <option value="1" @if ($status == 1) selected @endif>不需处理</option>
                    <option value="2" @if ($status == 2) selected @endif>处理完成</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>留言</td>
            <td>{{$content}}</td>
            <td>备注</td>
            <td><input type="text" class="form-control" name='remark' value="{{$remark}}" maxlength="256"/></td>
        </tr>
        <tr>
            <td>银行卡姓名</td>
            <td>{{$account_name}}</td>
        </tr>
        </tbody>
    </table>
</div>

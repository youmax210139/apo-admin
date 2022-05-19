<form id="userobserve-form" method="post">
<table class="table table-hover table-striped" id="user_observe_table">
    <tbody>
        <tr>
            <td class="text-right" width="190">用户名</td>
            <td class="text-left">
                <b>{{$username}}</b>
            </td>
        </tr>
        <tr>
            <td class="text-right">昵称</td>
            <td class="text-left">
                <b>{{$usernick}}</b>
            </td>
        </tr>
        <tr>
            <td class="text-right">重点观察</td>
            <td class="text-left">
                <input type="hidden" value="{{$id}}" name="user_id" id="user_observe_userid">
                <input type="checkbox" @if($comment!='') checked @endif onclick="changUserObserve(this)" id="user_observe_status" >
            </td>
        </tr>
        <tr id="user_observe_tr">
            <td class="text-right">备注</td>
            <td class="text-left">
                <textarea class="form-control" name="observe_comment" style="resize:vertical" placeholder="此备注信息不会展示给用户" id="user_observe_comment" maxlength="64" rows="3">{{$comment}}</textarea>
            </td>
        </tr>
        <tr>
            <td class="text-right">同时设置所有下级</td>
            <td class="text-left">
                <input type="checkbox" name="child" value="1" >
            </td>
        </tr>
    </tbody>
</table>
</form>
<style>
    #user_observe_table{
        margin-bottom: 0px;
    }
    #user_observe_table tr > td:nth-child(2){
        text-indent: 20px;
    }
</style>
<script>
    changUserObserve($('#user_observe_status'));
    function changUserObserve(obj) {
        var status = $(obj).prop('checked');
        if (status) {
            $('#user_observe_tr').show();
        } else {
            $('#user_observe_tr').hide();
            $('#user_observe_comment').val('');
        }
    }
</script>
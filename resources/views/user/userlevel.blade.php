<form id="userlevel-form" method="post">
<table class="table table-hover table-striped" id="user_level_table">
    <tbody>
        <tr>
            <td class="text-right" width="190">账户</td>
            <td class="text-left">
                <b>{{$username}}</b>
            </td>
        </tr>
        <tr>
            <td class="text-right">名称</td>
            <td class="text-left">
                <b>{{$usernick}}</b>
            </td>
        </tr>
        <tr>
            <td class="text-right">存款次数</td>
            <td class="text-left">
                <b></b>
            </td>
        </tr>
        <tr>
            <td class="text-right">存款总额</td>
            <td class="text-left">
                <b></b>
            </td>
        </tr>
        <tr>
            <td class="text-right">最大存款</td>
            <td class="text-left">
                <b></b>
            </td>
        </tr>
        <tr>
            <td class="text-right">累计消费</td>
            <td class="text-left">
                <b>{{$price}}</b>
            </td>
        </tr>
        <tr>
            <td class="text-right">提款次数</td>
            <td class="text-left">
                <b></b>
            </td>
        </tr>
        <tr>
            <td class="text-right">提款总额</td>
            <td class="text-left">
                <b></b>
            </td>
        </tr>
        <tr>
            <td class="text-right">出款打码量</td>
            <td class="text-left">
                <b></b>
            </td>
        </tr>
        <tr>
            <td class="text-right">打码量累计</td>
            <td class="text-left">
                <b></b>
            </td>
        </tr>
        <tr>
            <td class="text-right">归属层级</td>
            <td class="text-left">
                <input type="hidden" name="user_id" value="{{$id}}" id="user_level_userid">
                <select class="form-control"  name="user_level_id" id="user_level_id">
                    @foreach( $user_level_list as $level )
                    <option  value="{{$level->id}}" @if(!$level->status) disabled @endif @if($user_level['level']==$level->id) selected @endif>{{$level->name}}</option>
                    @endforeach
                </select>
                <label style="text-indent: 4px;margin-top: 3px;">
                    <input name="lock_user_level" type="checkbox" @if($user_level['from']=='db') checked @endif  id="lock_user_level" >
                    锁定层级
                </label>
            </td>
        </tr>
    </tbody>
</table>
</form>
<style>
    #user_level_table{
        margin-bottom: 0px;
    }
    #user_level_table tr > td:nth-child(2){
        text-indent: 20px;
    }
    #user_level_table tr label{
        font-weight: normal;
    }
</style>

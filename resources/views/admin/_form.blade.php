<div class="form-group">
    <label for="tag" class="col-md-3 control-label">用户昵称</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="usernick" id="tag" value="{{ $usernick }}" maxlength="20" />
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">用户名</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="username" id="tag" value="{{ $username }}" maxlength="20" />
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">密码</label>
    <div class="col-md-5">
        <input type="password" class="form-control" name="password" id="tag" value="" maxlength="20" placeholder="字母和数字组成的 6-20 位密码"/>
    </div>
</div>

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">确认密码</label>
    <div class="col-md-5">
        <input type="password" class="form-control" name="password_confirmation" id="tag" value="" maxlength="20" placeholder="字母和数字组成的 6-20 位密码"/>
    </div>
</div>


<div class="form-group">
    <label for="tag" class="col-md-3 control-label">角色列表</label>
        <div class="col-md-6">
            @if(isset($id)&&$id==1)
                <div class="col-md-4" style="margin-left:-16px;margin-top:6px;">
                超级管理员
                </div>
            @else
            @foreach($all_roles as $role)
            <div class="col-md-4" style="margin-left:-16px;margin-top:6px;">
                <label for="inputChekbox{{$role->id}}" class="checkbox-inline">
                    <input class="form-actions"
                               @if(is_object($roles) && $roles->contains($role->id))
                           checked
                           @endif
                           id="inputChekbox{{$role->id}}" type="Checkbox" value="{{$role->id}}"
                           name="roles[]">
                    {{$role->name}}
                </label>
            </div>
            @endforeach
            @endif
        </div>

</div>


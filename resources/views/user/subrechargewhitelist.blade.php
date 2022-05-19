<!-- form start -->
<form id='subrecharge-form' class="form-horizontal" role="form" method="POST">
    <div class="form-group">
        <label for="tag" class="col-md-3 control-label">用户名</label>
        <div class="col-md-6 control-label">
            <p class="text-left">{{$user->username}}</p>
        </div>
    </div>
    <div class="form-group">
        <label for="tag" class="col-md-3 control-label">是否白名单</label>
        <div class="col-md-6">
            <p class="help-block">
                <code>
                    @if($user->sub_recharge_status==1)
                        直属下级充值白名单
                    @elseif($user->sub_recharge_status==2)
                        所有下级充值白名单
                    @else
                        否
                    @endif
                </code>
            </p>
        </div>
    </div>
    @if($user->sub_recharge_status)
        <div class="form-group">
            <label class="col-md-3 control-label checkbox-inline text-bold">取消白名单方式</label>
            <div class="col-md-6">
                <div class="radio">
                    <label>
                        <input type="radio" name="type" value="0" checked="">
                        仅取消此会员，不取消其下级
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="type" value="1">
                        取消此会员和直属下级
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="type" value="2">
                        取消此会员和所有下级
                    </label>
                </div>
            </div>
        </div>
    @else
        <div class="form-group">
            <label class="col-md-3 control-label checkbox-inline text-bold">添加白名单方式</label>
            <div class="col-md-6">
                <div class="radio">
                    <label>
                        <input type="radio" name="status" value="0" checked="">
                        直属下级充值白名单
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="status" value="1">
                        所有下级充值白名单
                    </label>
                </div>
            </div>

        </div>
        <div class="form-group">
            <label class="col-md-3 control-label checkbox-inline text-bold">应用范围</label>
            <div class="col-md-6">
                <div class="radio">
                    <label>
                        <input type="radio" name="type" value="0" checked="">
                        仅此会员
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="type" value="1">
                        此会员和直属下级
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="type" value="2">
                        此会员和所有下级
                    </label>
                </div>
            </div>
        </div>
    @endif
    <input type="hidden" name="id" value="{{$user->id}}">
</form>

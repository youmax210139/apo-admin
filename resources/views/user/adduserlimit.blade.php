<form class="form-horizontal" id="adduserlimit-form" role="form" method="POST">
    <div class="form-group">
        <label for="tag" class="col-md-3 control-label">用户名</label>
        <div class="col-md-6">
            <p class="help-block">{{ $user->username }}</p>
        </div>
    </div>

    <div class="form-group">
        <label for="tag" class="col-md-3 control-label">当前开户限额</label>
        <div class="col-md-6">
            <p class="help-block">{{$total_limit}}</p>
        </div>
    </div>

    <div class="form-group">
        <label for="tag" class="col-md-3 control-label">已经使用</label>
        <div class="col-md-6">
            <p class="help-block">{{$used_limit}}</p>
        </div>
    </div>
    <div class="form-group">
        <label for="tag" class="col-md-3 control-label">增加人数</label>
        <div class="col-md-6">
            <input type="hidden" name="id" value="{{$user->id}}">
            <input type="number" class="form-control" name="num" value="0">
        </div>
    </div>
</form>




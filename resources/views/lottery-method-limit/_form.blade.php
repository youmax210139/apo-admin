
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">玩法类型</label>
    <div class="col-md-5 control">
        {{$lottery_method->lottery_method_category_name}}
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">玩法名称</label>
    <div class="col-md-5 control">
        {{$lottery_method->method_name}}
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">受限彩种</label>
    <div class="col-md-5">
        <select name="lottery_id" class="form-control">
            <option value="0">所有彩种</option>
             @foreach($lottery as $v)
            <option value="{{ $v->id }}">{{ $v->name }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">单注最低投注</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="project_min" id="tag" value="{{ $project_min }}" />
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">单注最高投注</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="project_max" id="tag" value="{{ $project_max }}"  />
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">单项最高投注</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="issue_max" id="tag" value="{{ $issue_max }}"  />
    </div>
</div>

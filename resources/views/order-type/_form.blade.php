

<div class="form-group">
    <label for="tag" class="col-md-3 control-label">ID</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="id" id="id" value="{{ $id }}" maxlength="64" >
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">名称</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="name" id="name" value="{{ $name }}" maxlength="64" >
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">唯一标识</label>
    <div class="col-md-5">
        <input type="text" class="form-control" name="ident" id="ident" value="{{ $ident }}" maxlength="64" >
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label checkbox-inline text-bold">类别</label>
    <div class="col-md-5">
        <select name="category" class="form-control" data-bv-field="category">
            <option value="0" @if($category==0) selected @endif>请选择</option>
            <option value="1" @if($category==1) selected @endif>彩票账变</option>
            <option value="2" @if($category==2) selected @endif>充提账变</option>
            <option value="3" @if($category==3) selected @endif>第三方账变</option>
        </select>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">前台显示</label>
    <div class="col-md-6">
        <div class="radio">
            <label>
                <input type="radio" name="display" value="0" @if($display==0)  checked="" @endif>
                否
            </label>
            <label>
                <input type="radio" name="display" value="1" @if($display==1)  checked="" @endif>
                是
            </label>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">余额操作</label>
    <div class="col-md-6">
        <div class="radio">
            <label>
                <input type="radio" name="operation" value="0" @if($operation==0)  checked="" @endif>
                无操作
            </label>
            <label>
                <input type="radio" name="operation" value="1" @if($operation==1)  checked="" @endif>
                增加
            </label>
            <label>
                <input type="radio" name="operation" value="2" @if($operation==2)  checked="" @endif>
                减少
            </label>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">冻结金额操作</label>
    <div class="col-md-6">
        <div class="radio">
            <label>
                <input type="radio" name="hold_operation" value="0" @if($hold_operation==0)  checked="" @endif>
                无操作
            </label>
            <label>
                <input type="radio" name="hold_operation" value="1" @if($hold_operation==1)  checked="" @endif>
                增加
            </label>
            <label>
                <input type="radio" name="hold_operation" value="2" @if($hold_operation==2)  checked="" @endif>
                减少
            </label>
        </div>
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">概述</label>
    <div class="col-md-5">
        <textarea name="description" class="form-control" rows="3">{{ $description }}</textarea>
    </div>
</div>
@section('js')
    <script type="text/javascript">
        $(function(){

        });
    </script>
@stop
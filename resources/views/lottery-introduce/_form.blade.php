    <div class="form-group">
        <label class="col-md-3 control-label">标题</label>
        <div class="col-md-6">
            <input type="text" class="form-control" name="subject" value="{{ $subject }}" maxlength="64" autocomplete="off">
        </div>
    </div>
<div class="form-group">
    <label class="col-md-3 control-label">内容</label>
    <div class="col-md-6">
        <textarea class="form-control" id="content" name="content">{{ $content }}</textarea>
    </div>
</div>
@if(empty($lottery))
    <input type="hidden" name="lottery_id" value="0">
@else
    <div class="form-group">
        <label for="name" class="col-md-3 control-label">彩种</label>
        <div class="col-md-6">
            <select name="lottery_id" class="form-control">
                @foreach($lottery as $v)
                    <option value="{{ $v->id }}" @if($v->id==$lottery_id) selected @endif>{{ $v->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
@endif
@if(empty($lottery))
    <div class="form-group">
        <label class="col-md-3 control-label">排序</label>
        <div class="col-md-6">
            <input type="text" class="form-control" name="sort" value="{{ $sort }}" maxlength="5" autocomplete="off">
        </div>
    </div>
@else
    <input type="hidden" name="sort" value="0">
@endif
<div class="form-group">
    <label class="col-md-3 control-label checkbox-inline text-bold">状态</label>
    <div class="col-md-6">
        <div class="radio">
            <label class="radio-inline">
                <input type="radio" name="status" value="0" @if(!$status) checked @endif>
                禁用
            </label>
            <label class="radio-inline">
                <input type="radio" name="status" value="1" @if($status) checked @endif>
                启用
            </label>
        </div>
    </div>
</div>
@section('js')
    <script src="/assets/plugins//ckeditor/ckeditor.js"></script>
    <script type="text/javascript">
        $(function () {
            CKEDITOR.replace('content', {
                language: 'zh-cn',
                uiColor: '#9AB8F3'
            });
        })
    </script>
@stop

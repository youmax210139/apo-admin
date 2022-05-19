<div class="form-group">
    <label for="tag" class="col-md-3 control-label">公告标题</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="subject" value="{{ $subject }}" maxlength="64" >

    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">发布时间</label>
    <div class="col-md-6">
        <input type="text" class="form-control" id="published_at" name="published_at" value="{{ $published_at }}" maxlength="10" autocomplete="off">
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">结束时间</label>
    <div class="col-md-6">
        <input type="text" class="form-control" id="end_at" name="end_at" value="{{ $end_at }}" maxlength="10" autocomplete="off">
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">排序</label>
    <div class="col-md-6">
        <input type="text" class="form-control" style="width: 8%" name="sort" value="{{ $sort }}" maxlength="5">
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">是否置顶</label>
    <div class="col-md-6">
        <div class="radio">
            <label>
                <input type="radio" name="is_top" value="0" @if(!$is_top) checked @endif>
                否
            </label>

            <label>
                <input type="radio" name="is_top" value="1" @if($is_top) checked @endif>
                是
            </label>
        </div>
    </div>
</div>
<div class="form-group">

    <div class="col-md-12">
        <textarea name="content" id="contentEditor" style="overflow:scroll; max-height:300px" class="form-control" rows="3">{{ $content }}</textarea>
    </div>
</div>
 @section('js')
<script src="/assets/plugins/ckeditor/ckeditor.js?20190715"></script>
<script src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
<script>

    $(function(){
        CKEDITOR.editorConfig = function( config ) {
            config.toolbarGroups = [
                { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
                { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
                { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
                { name: 'forms', groups: [ 'forms' ] },
                '/',
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
                { name: 'links', groups: [ 'links' ] },
                { name: 'insert', groups: [ 'insert' ] },
                '/',
                { name: 'styles', groups: [ 'styles' ] },
                { name: 'colors', groups: [ 'colors' ] },
                { name: 'tools', groups: [ 'tools' ] },
                { name: 'others', groups: [ 'others' ] },
                { name: 'about', groups: [ 'about' ] }
            ];
        };
          CKEDITOR.replace( 'contentEditor' , {
              language: 'zh-cn'
          });
    })

    laydate.skin('lynn');
    laydate({
        elem: '#published_at',
        event: 'focus',
        format: 'YYYY-MM-DD hh:mm:ss',
        istime: true,
        istoday: true,
        zindex:2
    });
    laydate({
        elem: '#end_at',
        event: 'focus',
        format: 'YYYY-MM-DD hh:mm:ss',
        istime: true,
        istoday: true,
        zindex:2
    });
</script>
@stop
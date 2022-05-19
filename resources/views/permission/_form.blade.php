


<div class="form-group">
    <label for="tag" class="col-md-3 control-label">权限规则</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="rule" id="tag" value="{{ $rule }}" maxlength="64" />
        <input type="hidden" class="form-control" name="parent_id" id="tag" value="{{ $parent_id }}" />
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">权限名称</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="name" id="tag" value="{{ $name }}" maxlength="128" />
    </div>
</div>
@if($parent_id == 0 )
{{--图标修改--}}
    <link rel="stylesheet" href="/assets/plugins/bootstrap-iconpicker/icon-fonts/font-awesome-4.2.0/css/font-awesome.min.css"/>
    <link rel="stylesheet" href="/assets/plugins/bootstrap-iconpicker/bootstrap-iconpicker/css/bootstrap-iconpicker.min.css"/>

    <div class="form-group">
    <label for="tag" class="col-md-3 control-label">图标</label>
    <div class="col-md-6">
        <!-- Button tag -->
        <button class="btn btn-default" name="icon" data-iconset="fontawesome" data-icon="{{ $icon?$icon:'fa-sliders' }}" role="iconpicker"></button>
    </div>

    </div>
@section('js')
    <script type="text/javascript" src="/assets/plugins/bootstrap-iconpicker/bootstrap-iconpicker/js/iconset/iconset-fontawesome-4.2.0.min.js"></script>
    <script type="text/javascript" src="/assets/plugins/bootstrap-iconpicker/bootstrap-iconpicker/js/bootstrap-iconpicker.min.js"></script>
@stop
@endif
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">权限概述</label>
    <div class="col-md-6">
        <textarea name="description" class="form-control" rows="3">{{ $description }}</textarea>
    </div>
</div>


<div class="main animsition">
    <div class="container-fluid">
        <div class="row">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">基本信息</h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="tag" class="col-md-3 control-label">角色名称</label>
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="name" id="tag" value="{{ $name }}" maxlength="16" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tag" class="col-md-3 control-label">角色概述</label>
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="description" id="tag" value="{{ $description }}" maxlength="32" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">权限列表</h3>
                </div>
                <div class="panel-body">
                     <table class="table table-bordered table-hover table-condensed">
                         @foreach($permission_all['root'] as $v)
                                 @if(!isset($permission_all['second'][$v->id])) @continue @endif
                                 @forelse($permission_all['second'][$v->id] as $val)
                             <tr>
                                 @if($val == current($permission_all['second'][$v->id]))
                                 <td rowspan="{{count($permission_all['second'][$v->id])}}" class="col-md-2" style="vertical-align:middle !important;text-align:left;">
                                     <label for="inputChekbox{{$v->id}}" class="text-center">
                                         <input class="form-actions 1st_level"
                                                @if(in_array($v->id,$permissions))
                                                checked
                                                @endif
                                                id="inputChekbox{{$v->id}}" type="Checkbox" value="{{$v->id}}"
                                                1st_id="{{$v->id}}"
                                                name="permissions[]" />
                                         {{$v->name}}
                                     </label>
                                 </td>
                                 @endif
                                 <td class="col-md-2" style="vertical-align:middle !important;text-align:left;">
                                     <label for="inputChekbox{{$val->id}}" class="checkbox-inline">
                                         <input class="form-actions 2nd_level"
                                                @if(in_array($val->id, $permissions))
                                                checked
                                                @endif
                                                id="inputChekbox{{$val->id}}"
                                                parent_1st_id="{{$v->id}}"
                                                2nd_id="{{$val->id}}" type="Checkbox" value="{{$val->id}}"
                                                name="permissions[]" />
                                         {{$val->name}}
                                     </label>
                                 </td>
                                 <td>
                                     @if(isset($permission_all['third'][$val->controller]))
                                         @foreach($permission_all['third'][$val->controller] as $vv)
                                             <label for="inputChekbox{{$vv->id}}" class="checkbox-inline label_3rd">
                                                 <input class="form-actions 3rd_level"
                                                        @if(in_array($vv->id,$permissions))
                                                        checked
                                                        @endif
                                                        parent_2nd_id="{{$val->id}}"
                                                        id="inputChekbox{{$vv->id}}" type="Checkbox" value="{{$vv->id}}"
                                                        name="permissions[]" />
                                                 {{$vv->name}}
                                             </label>
                                         @endforeach
                                     @endif
                                 </td>
                             </tr>
                                  @empty
                                     <tr>
                                         <td class="col-md-2" style="vertical-align:middle !important;text-align:left;">
                                             <label for="inputChekbox{{$v->id}}" class="text-center">
                                                 <input class="form-actions 1st_level"
                                                        @if(in_array($v->id,$permissions))
                                                        checked
                                                        @endif
                                                        id="inputChekbox{{$v->id}}" type="Checkbox" value="{{$v->id}}"
                                                        1st_id="{{$v->id}}"
                                                        name="permissions[]" />
                                                 {{$v->name}}
                                             </label>
                                         </td>
                                     </tr>
                                  @endforelse
                         @endforeach
                     </table>
                    <div class="row text-center"><label><input class="check_all" type="checkbox"> 全选</label></div>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    .checkbox-inline+.checkbox-inline, .radio-inline+.radio-inline{margin-left:0px;}
    .container-fluid{margin-bottom: 5px;}
    label{padding:0 30px 5px 0;}
    label.label_3rd{padding:0 30px 0 28px;}
</style>
@section('js')
    <script type="text/javascript">
        $(function(){
            $(".check_all").click(function () {
                if($(this).is(':checked')){
                    $("table input[type='checkbox']").prop('checked',true);
                }else
                $("table input[type='checkbox']").prop('checked',false);
            })
            //三级分类被选中，自动勾选对应的二级和一级分类
            $(".3rd_level").click(function(){
                if($(this).prop('checked')){
                    var value = $(this).attr("parent_2nd_id");
                    $(".2nd_level").each(function(){
                        if($(this).attr("2nd_id") == value){
                            $(this).prop("checked",true);
                            var value_inner = $(this).attr("parent_1st_id");
                            $(".1st_level").each(function(){
                                if($(this).attr("1st_id") == value_inner){
                                    $(this).prop("checked",true);
                                }
                            });
                        }
                    });
                }
            });

            //二级分类被选中，自动勾选对应的一级分类
            $(".2nd_level").click(function(){
                if($(this).prop('checked')){
                    var value = $(this).attr("parent_1st_id");
                    $(".1st_level").each(function(){
                        if($(this).attr("1st_id") == value){
                            $(this).prop("checked",true);
                        }
                    });
                }
            });
        });
    </script>
@stop
@extends('layouts.base')

@section('title','批量添加IP')

@section('function','登录IP黑名单')
@section('function_link', '/ipblacklist/')

@section('here','批量添加IP')

{{--@section('pageDesc','DashBoard')--}}
@section('content')
    <div class="main animsition" xmlns="http://www.w3.org/1999/html">
        <div class="container-fluid">
            <div class="row">
                @include('partials.errors')
                @include('partials.success')
                <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data" action="/ipblacklist/create" onsubmit="return checkForm()">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="type" value="many">

                    <div class="main animsition">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">批量添加IP记录</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="ip" class="col-md-3 control-label">说明</label>
                                            <div class="col-md-5">
                                                <p class="text-red">CSV格式：</p>
                                                <p>每行只能有 <span class="text-red">IP,备注</span> 两者都不能为空，使用<span class="text-red">英文逗号,分隔</span>。范例如下：</p>
                                                <p>xxx.xxx.xxx.xxx,风险IP</p>
                                                <p>yyy.yyy.yyy.yyy,风险IP</p>
                                                <p class="text-red">建议：</p>
                                                <p>事先在EXCEL整理好资料，再导出CSV、复制/粘贴至下方输入栏，减少因格式错误而发生上传失败的问题。</p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="ip" class="col-md-3 control-label">IP黑名单CSV内容</label>
                                            <div class="col-md-5">
                                                <textarea class="form-control" name="ips_many" id="ips_many" placeholder="每行一条记录&#10;xxx.xxx.xxx.xxx,风险IP" rows="10" /></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="ip" class="col-md-3 control-label">上传CSV文件</label>
                                            <div class="col-md-5">
                                                <input type="file" name="ips_file" id="ips_file" value="" accept=".csv">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-offset-5">
                           <button type="button" class="btn btn-warning btn-md" onclick="history.back()">
                               <i class="fa fa-minus-circle"></i>
                               取消
                           </button>
                            <button type="submit" class="btn btn-primary btn-md">
                                <i class="fa fa-plus-circle"></i>
                                添加
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    function checkForm() {
        var ips_many = $("#ips_many").val();
        var ips_file = $("#ips_file").val();
        if(ips_many == '' && ips_file == '') {
            BootstrapDialog.alert("请输入IP黑名单CSV内容 或者 选择上传CSV文件");
            return false;
        } else {
            return true;
        }
    }
</script>
@stop
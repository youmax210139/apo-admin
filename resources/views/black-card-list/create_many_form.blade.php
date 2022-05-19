@extends('layouts.base')
@section('title','批量添加银行卡黑名单')
@section('function','银行卡黑名单管理')
@section('function_link', '/blackcardlist/')
@section('here','批量添加黑名单')

@section('content')
    <div class="main animsition" xmlns="http://www.w3.org/1999/html">
        <div class="container-fluid">
            <div class="row">
                @include('partials.errors')
                @include('partials.success')
                <form class="form-horizontal" role="form" method="POST" enctype="multipart/form-data" action="/blackcardlist/create" onsubmit="return checkForm()">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="type" value="many">

                    <div class="main animsition">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">批量添加银行卡记录</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="ip" class="col-md-3 control-label">说明</label>
                                            <div class="col-md-5">
                                                <p class="text-red">CSV格式：</p>
                                                <p>每行只能有 <span class="text-red">账户名,卡号,备注</span> 且都不能为空，使用<span class="text-red">英文逗号,分隔</span>。范例如下：</p>
                                                <p>用户一,123456789,恶投用户</p>
                                                <p>用户二,987654321,风险用户</p>
                                                <p class="text-red">建议：</p>
                                                <p>一、事先在EXCEL中整理好资料，再导出CSV、复制/粘贴至下方输入栏，减少因格式错误而发生上传失败的问题。</p>
                                                <p>二、少量多次上传，若发生错误，则会整批退回。</p>
                                                <p>三、单次上传记录上限：1000条</p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="ip" class="col-md-3 control-label">内容</label>
                                            <div class="col-md-5">
                                                <textarea class="form-control" name="banks_many" id="banks_many" placeholder="每行一条记录&#10;用户一,123456789,风险用户" rows="10" /></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="ip" class="col-md-3 control-label">上传CSV文件</label>
                                            <div class="col-md-5">
                                                <input type="file" name="banks_file" id="banks_file" value="" accept=".csv">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-offset-5">
                           <button type="button" class="btn btn-warning btn-md" onclick="location.href = '/blackcardlist/';">
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
        var banks_many = $("#banks_many").val();
        var banks_file = $("#banks_file").val();
        if(banks_many == '' && banks_file == '') {
            BootstrapDialog.alert("请输入银行卡黑名单CSV内容 或者 选择上传CSV文件");
            return false;
        } else {
            return true;
        }
    }
</script>
@stop
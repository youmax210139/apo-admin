<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        ×
    </button>
    <h4 class="modal-title">详情</h4>
</div>
<div class="modal-body">
    <div class="main animsition">
        <div class="container-fluid">
            <div class="row">
                <div class="">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">请求日志详情</h3>
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped table-bordered table-hover">
                                <tr>
                                    <td>用户名</td>
                                    <td>
                                        {{$username}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>访问路径</td>
                                    <td>{{$path}}</td>
                                </tr>
                                <tr>
                                    <td>时间</td>
                                    <td>{{$created_at}}</td>
                                </tr>
                                <tr>
                                    <td>请求数据</td>
                                    <td><div style="max-height:200px; overflow-y: auto;word-wrap: break-word; width: 700px;"><?php echo "<pre>";print_r(json_decode($request,true));echo "</pre>"; ?></div></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <h4 class="modal-title">详情</h4>
</div>
<div class="modal-body">
    <div class="main animsition">
        <div class="container-fluid">
            <div class="row">
                <div class="">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">异常行为详情</h3>
                        </div>
                        <div class="panel-body">
                            <table class="table table-striped table-bordered table-hover">
                                <tr>
                                    <td>事件类型</td>
                                    <td>{{$type}}</td>
                                </tr>
                                <tr>
                                    <td>描述</td>
                                    <td>
                                        <div style="max-height:200px; overflow-y: auto;word-wrap: break-word; width: 700px;">{{$description}}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>时间</td>
                                    <td>{{$created_at}}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

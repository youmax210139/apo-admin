<div class="main animsition">
    <div class="container-fluid">
        <div class="row">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">基本信息</h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="ip" class="col-md-3 control-label">IP地址</label>
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="ip" id="ip" value="{{ $ip }}" placeholder="IP 192.168.10.11 或者 掩码 192.168.1/24" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="remark" class="col-md-3 control-label">备注</label>
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="remark" id="remark" value="{{ $remark }}" maxlength="32" placeholder="名字/XX办公室/运营/财务/客服/技术" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
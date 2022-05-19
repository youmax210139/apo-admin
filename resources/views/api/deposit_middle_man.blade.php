@include('layouts.baseHeader')
<div class="container" style="margin-top: 20px;">
    @if($error_msg)
        <div class="alert alert-danger" role="alert">{{$error_msg}}</div>
    @endif
    @if($res)
        <div class="panel panel-success">
            <div class="panel-heading">充值接口正常</div>
            <div class="panel-body">

                <dl>
                    <dt>状态码</dt>
                    <dd>{{ var_export($post)}}</dd>
                </dl>
                <dl>
                    <dt>状态码</dt>
                    <dd>{{$res->getStatusCode()}}</dd>
                </dl>
                <dl>
                    <dt>返回值</dt>
                    <dd>{{$res->getBody()->getContents()}}</dd>
                </dl>

            </div>
        </div>
    @endif
    @if(isset($e))
        <div class="panel panel-danger">
            <div class="panel-heading">充值接口异常</div>
            <div class="panel-body">
                <dl>
                    <dt>错误代码</dt>
                    <dd>{{$e->getCode()}}</dd>
                </dl>
                <dl>
                    <dt>错误消息</dt>
                    <dd>{{$e->getMessage()}}</dd>
                </dl>
            </div>
        </div>
    @endif
</div>

@include('layouts.baseFooter')

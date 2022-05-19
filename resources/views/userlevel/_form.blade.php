@if (!empty($id))
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">层级</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="id" value="{{ $id }}" disabled="true">
    </div>
</div>
@endif
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">层级名称</label>
    <div class="col-md-6">
        <input type="text" class="form-control" name="name" value="{{ $name }}" placeholder="第一层" maxlength="64">
    </div>
</div>
<div class="form-group">
    <label for="tag" class="col-md-3 control-label">用户注册日期</label>
    <div class="col-md-6">
        <div class="input-daterange input-group">
            <input type="text" class="form-control form_datetime" name="register_start_time" value="{{ $register_start_time }}"  id='register_start_time'   placeholder="起始日">
            <span class="input-group-addon">~</span>
            <input type="text" class="form-control form_datetime" name="register_end_time" value="{{ $register_end_time }}"  id='register_end_time'  placeholder="截止日">
        </div>
    </div>
</div>
<div class="form-group">
    <label class="col-md-3 control-label checkbox-inline text-bold">存款</label>
    <div class="col-md-6 ">
        <div class="col-md-4" style="padding-left: 0px;">
            <input type="text" class="form-control form_datetime" name="deposit_times" value="{{ $deposit_times }}"  id='deposit_times'   placeholder="次数">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form_datetime" name="deposit_count_amount" value="{{ $deposit_count_amount }}"  id='deposit_count_amount'   placeholder="总额">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form_datetime" name="deposit_max_amount" value="{{ $deposit_max_amount }}"  id='deposit_max_amount'   placeholder="最大金额">
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-md-3 control-label checkbox-inline text-bold">提款</label>
    <div class="col-md-6 ">
        <div class="col-md-4" style="padding-left: 0px;">
            <input type="text" class="form-control form_datetime" name="withdrawal_times" value="{{ $withdrawal_times }}"  id='withdrawal_times'   placeholder="次数">
        </div>
        <div class="col-md-4">
            <input type="text" class="form-control form_datetime" name="withdrawal_count_amount" value="{{ $withdrawal_count_amount }}"  id='withdrawal_count_amount'   placeholder="总额">
        </div>
    </div>
</div>

<div class="form-group">
    <label class="col-md-3 control-label checkbox-inline text-bold">消费金额</label>
    <div class="col-md-6 radio">
        <input type="text" class="form-control" name="expense_count_amount" value="{{ $expense_count_amount }}" placeholder="消费金额">
    </div>
</div>

<div class="form-group">
    <label class="col-md-3 control-label checkbox-inline text-bold">备注</label>
    <div class="col-md-6 radio">
        <input type="text" class="form-control" name="remark" value="{{ $remark }}" placeholder="备注">
    </div>
</div>


<div class="form-group">
    <label class="col-md-3 control-label checkbox-inline text-bold">状态</label>
    <div class="col-md-6">
        <label class="radio-inline">
            <input type="radio" value="1" name="status" @if ($status != 0 ) checked @endif>
            开启
        </label>
        <label class="radio-inline">
            <input type="radio" value="0" name="status" @if ($status == 0) checked @endif>
            关闭
        </label>
    </div>
</div>


@section('css')
    <style>
        hr {border:1px dotted #cccccc;}
    </style>
@stop

@section('js')
<script src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
<script>
    laydate.skin('lynn');
    var layConfig ={
        elem: '#register_start_time',
        event: 'focus',
        format: 'YYYY-MM-DD hh:mm:ss',
        istime: true,
        istoday: true,
        zindex:2
    };
    laydate(layConfig);

    layConfig.elem = '#register_end_time';
    laydate(layConfig);
</script>
@stop

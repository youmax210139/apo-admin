@extends('layouts.base')

@section('title','用户管理')

@section('function','用户管理')
@section('function_link', '/user/')

@section('here','人工充值')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
<div class="main animsition">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title">人工充值</h3>
                    </div>
                <div class="panel-body">
                @include('partials.errors')
                @include('partials.success')
                <form role="form" class="form-horizontal" method="POST" id="defaultForm" onsubmit="return false;">
                    <div class="form-group">
                        <label class="col-md-3 control-label">用户名</label>
                        <div class="col-md-6 control-label">
                            <p class="text-left">{{$user->username}}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">可用余额</label>
                        <div class="col-md-6 control-label">
                            <p class="text-left"><code>{{$user->fund->balance}}</code></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">冻结金额</label>
                        <div class="col-md-6 control-label">
                            <p class="text-left"><code>{{$user->fund->hold_balance}}</code></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">充值金额(小写)</label>
                        <div class="col-md-3">
                            <input onkeyup="checkMoney(this, 'chineseMoney', 10000000.0000)" type="text" name="money" class="form-control" placeholder="输入金额">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">充值金额(大写)</label>
                        <div class="col-md-6 control-label">
                            <p class="text-left"><code id="chineseMoney"></code></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label radio-inline text-bold">账变类型</label>
                        <div class="col-md-6">
                            @foreach($order_types as $_type_key => $order_type)
                            <div class="radio">
                                <label>
                                    <input type="radio" name="ordertype" @if($_type_key == 0) checked="" @endif value="{{$order_type->ident}}">
                                    {{$order_type->name}}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="form-group" id="div_payment" style="display: none">
                        <label for="tag" class="col-md-3 control-label">渠道/通道</label>
                        <div class="col-md-5 form-inline">
                            <select name="payment_category_id" class="form-control" style="width: 30%">
                                <option value="">请选择充值渠道</option>
                                @foreach($payment_categories as $payment_category)
                                    <option value="{{ $payment_category->id }}">{{ $payment_category->name }}</option>
                                @endforeach
                            </select>
                            <select name="payment_channel_id" class="form-control" style="width: 60%">
                                <option value="">请选择充值通道</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="activity" style="display: none">
                        <label for="tag" class="col-md-3 control-label">相关活动</label>
                        <div class="col-md-5">
                            <select name="activity_id" class="form-control">
                                <option value="">请选择相关活动</option>
                                @foreach($activities as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tag" class="col-md-3 control-label">充值备注</label>
                        <div class="col-md-5">
                            <input type="text" name="description" class="form-control" placeholder="请输入充值原因">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tag" class="col-md-3 control-label">卡号备注</label>
                        <div class="col-md-5">
                            <input type="text" name="cardnotice" class="form-control" placeholder="此备注信息不予展示给用户">
                        </div>
                    </div>

                    <div class="form-group margin">
                        <div class="col-md-7 col-md-offset-3">
                            <button type="submit" class="btn btn-primary btn-md" id="search_btn">用户充值</button>
                        </div>
                    </div>
                </form>
                </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-confrim" tabIndex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    ×
                </button>
                <h4 class="modal-title">充值信息确认</h4>
            </div>
            <div class="modal-body form-horizontal">
                <div class="form-group">
                    <label class="col-md-3 control-label">用户名</label>
                    <div class="col-md-6 control-label">
                        <p class="text-left">{{$user->username}}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">可用余额</label>
                    <div class="col-md-6 control-label">
                        <p class="text-left"><i>{{$user->fund->balance}}</i></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">冻结金额</label>
                    <div class="col-md-6 control-label">
                        <p class="text-left"><i>{{$user->fund->holdbalance}}</i></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">充值金额(小写)</label>
                    <div class="col-md-6 control-label">
                        <p class="text-left"><b><code id="confrim-money"></code></b></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">充值金额(大写)</label>
                    <div class="col-md-6 control-label">
                        <p class="text-left"><code id="confrim-chineseMoney"></code></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">账变类型</label>
                    <div class="col-md-6 control-label">
                        <p class="text-left"><span id="confrim-ordertype"></span></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">充值备注</label>
                    <div class="col-md-6 control-label">
                        <div class="text-left">
                            <p id="confrim-description"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <form class="confrimForm" id="confrimForm" method="POST" action="/user/recharge">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="userid" value="{{$user->id}}">
                    <input type="hidden" name="money" value="">
                    <input type="hidden" name="ordertype" value="">
                    <input type="hidden" name="ordertypetext" value="">
                    <input type="hidden" name="description" value="">
                    <input type="hidden" name="cardnotice" value="">
                    <input type="hidden" name="activity_id" value="">
                    <input type="hidden" name="payment_channel_id" value="">
                    <input type="hidden" name="payment_category_id" value="">
                    <button type="button" class="btn btn-default" data-dismiss="modal">重新填写</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa"></i> 确认无误充值
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop
@section('js')
<script>
  $(document).ready(function() {
      $("input[type='radio']").click(function () {
          if($(this).val()=='CXCZ'){
              $("#activity").show();
              $("#div_payment").hide();
          }else if($(this).val()=='SFRGCZ'){//三方充值加款
              $("#activity").hide();
              $("#div_payment").show();
          }else{
              $("#activity").hide();
              $("#div_payment").hide();
          }
      });

      // 彩种选择onchange，联动玩法显示与否效果,隐藏重置为初始值
      $('select[name="payment_category_id"]').change(function () {
          if ($(this).val() >= 1) {
              payment_change($(this).find("option:selected").attr("value"));
          } else {
              $('select[name="payment_channel_id"]').html("<option value=''>通道列表</option>")
          }
      });

      function payment_change(category_id) {
          console.log(category_id);
          //玩法显示
          var payment_channels = JSON.parse('{!! $payment_channels !!}');
          var html = "<option value=''>通道列表</option>";
          $.each(payment_channels, function (i, v) {
              console.log(v);
              if(v.payment_category_id == category_id){
                  html += "<option value='" + v.id + "'>" + v.name + " [ "+ v.front_name +" ]</option>";
              }
          });
          $('select[name="payment_channel_id"]').html(html);
      }

    $('#defaultForm')
        .bootstrapValidator({
            message: '该数据不可用',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
            	money: {
                    validators: {
                        notEmpty: {
                            message: '请输入充值金额!'
                        },
                        numeric:{
                            message: '金额必须是数字类型!'
                        }
                    }
                },description: {
                    validators: {
                        notEmpty: {
                            message: '请输入原因!'
                        }
                    }
                },
            }
        }).on('success.form.bv', function(e) {
                 var money = $('#defaultForm input[name="money"]').val();
                 var ordertype = $('#defaultForm input:checked').val();
                 var ordertype_text = $('#defaultForm input:checked').parent().text();
                 var description = $('#defaultForm input[name="description"]').val();
                 var cardnotice = $('#defaultForm input[name="cardnotice"]').val();
                 
                 $("#confrim-money").html(money);
                 $('#confrimForm input[name="money"]').val(money);
                 $("#confrim-chineseMoney").html($("#chineseMoney").html());
                 $("#confrim-ordertype").html(ordertype_text);
                 $('#confrimForm input[name="ordertype"]').val(ordertype);
                 $('#confrimForm input[name="ordertypetext"]').val(ordertype_text);
                 $("#confrim-description").html(description);
                 $('#confrimForm input[name="description"]').val(description);
                 $('#confrimForm input[name="cardnotice"]').val(cardnotice);
                 $('#confrimForm input[name="activity_id"]').val($('#defaultForm select[name="activity_id"]').val());
                 $('#confrimForm input[name="payment_category_id"]').val($('#defaultForm select[name="payment_category_id"]').val());
                 $('#confrimForm input[name="payment_channel_id"]').val($('#defaultForm select[name="payment_channel_id"]').val());



        	    $("#modal-confrim").modal();
                 $('#modal-confrim').on('hidden.bs.modal', function (e) {
                   $('#defaultForm :submit').prop( "disabled", false )
                  });
        	e.preventDefault();
        });
});
</script>
@stop
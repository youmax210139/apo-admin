@extends('layouts.base')

@section('title','用户管理')

@section('function','用户列表')
@section('function_link', '/user/')

@section('here','用户列表')

{{--@section('pageDesc','DashBoard')--}}

@section('content')
<div class="row">
<div class="col-sm-12">
     @include('partials.errors')
                @include('partials.success')
    <!--搜索框 Start-->
    <div class="box box-primary">
        <form class="form-horizontal" id="search">
            <div class="box-header with-border">
                <h3 class="box-title"><!--搜索查询区--></h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                        <i class="fa fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="box-body">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="username" class="col-sm-3 col-sm-3 control-label">用户名/ID</label>
                        <div class="col-sm-5">
                            <input type="text" value="{{request()->get('username')}}" class="form-control" name='username' placeholder="用户名/ID" />


                            <input type="hidden" name='id' value="{{ $id }}" />
                            <input type="hidden" name="is_search" value="0" />
                        </div>
                        <label class="control-label checkbox-inline col-sm-4" style="padding-top: 7px;">
                            <input type="checkbox" name="include_all" value="1" />
                            包含所有下级
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="created_date" class="col-sm-3 control-label">注册时间</label>
                        <div class="col-sm-9">
                            <div class="input-daterange input-group">
                                <input type="text" class="form-control form_datetime" name="created_start_date" id='created_start_date' value="" placeholder="开始时间">
                                <span class="input-group-addon">~</span>
                                <input type="text" class="form-control form_datetime" name="created_end_date" id='created_end_date' value="" placeholder="结束时间">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="user_group_id" class="col-sm-3 control-label">用户组别</label>
                        <div class="col-sm-5">
                            <select name="user_group_id" class="form-control">
                                <option value="all">所有组别</option>
                                @foreach($user_group as $item)
                                    <option value="{{ $item->id }}" @if($item->id==1) selected @endif>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label class="control-label checkbox-inline col-sm-4" style="padding-top: 7px;">
                            <input type="checkbox" name="sub_recharge_status" value="1" />
                            已开通下级充值
                        </label>
                    </div>

                    <div class="form-group">
                        <label for="frozen_date" class="col-sm-3 control-label">冻结时间</label>
                        <div class="col-sm-9">
                            <div class="input-daterange input-group">
                                <input type="text" class="form-control form_datetime" name="frozen_start_date" id='frozen_start_date' value="" placeholder="开始时间">
                                <span class="input-group-addon">~</span>
                                <input type="text" class="form-control form_datetime" name="frozen_end_date" id='frozen_end_date' value="" placeholder="结束时间">
                            </div>

                        </div>
                    </div>

                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="user_type_id" class="col-sm-3 control-label">用户类型</label>
                        <div class="col-sm-9">
                            <select name="user_type_id" class="form-control">
                                <option value="all">所有类型</option>
                                @foreach($user_type as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="balance" class="col-sm-3 control-label">可用余额</label>
                        <div class="col-sm-9">
                            <div class="input-daterange input-group">
                                <input type="text" class="form-control form_datetime" name="balance_min" placeholder="最小金额">
                                <span class="input-group-addon">~</span>
                                <input type="text" class="form-control form_datetime" name="balance_max" placeholder="最大金额">
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="box-footer">
                <div class="btn-group col-md-6">
                    <button type="submit" class="btn btn-primary col-sm-2 pull-right" id="search_btn"><i class="fa fa-search" aria-hidden="true"></i>查询</button>
                </div>
                <div class=" btn-group col-md-6">
                    <button type="reset" class="btn btn-default col-sm-2" ><i class="fa fa-refresh" aria-hidden="true"></i>重置</button>
                    <button type="button" class="btn btn-success" id="reload_table" style="float: right"><i class="fa fa-refresh" aria-hidden="true"></i>刷新</button>
                    @if(Gate::check('user/createshiwan'))
                    <button type="button" class="btn btn-default" id="create_shiwan" style="margin-left: 30px;"><i class="fa fa-refresh" aria-hidden="true"></i>生成试玩用户</button>
                    @endif
                </div>
            </div>
	   </form>
    </div>

    <div class="box box-primary">
        <div class="box-body">
            <ol class="breadcrumb" id="parent_tree" style="margin-bottom: 10px;display: none;font-weight: bold;padding: 5px">
            </ol>
            <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct">
                <thead>
                <tr>
                    <th class="hidden-sm" data-sortable="false"></th>
                    <th class="hidden-sm">编号</th>
                    <th class="hidden-sm" data-sortable="false">用户名</th>
                    <th class="hidden-sm" data-sortable="false">团队人数</th>
                    <th class="hidden-sm" data-sortable="false">级别</th>
                    <th class="hidden-sm" data-sortable="false">组别</th>
                    <th class="hidden-sm" data-sortable="true">返点</th>
                    <th class="hidden-sm" data-sortable="true">余额</th>
                    <th class="hidden-sm" data-sortable="false">冻结金额</th>
                    <th class="hidden-sm" data-sortable="false">下级充值权限</th>
                    <th class="hidden-sm">注册时间</th>
                    <th class="hidden-sm">最后登陆时间</th>
                    <th class="hidden-sm" data-sortable="false">账号状态</th>
                    <th class="hidden-sm" data-sortable="false">登陆状态</th>
                    <th class="hidden-sm" data-sortable="false">操作</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
    <style>
        .kickout{
            cursor: pointer;
        }
        #parent_tree > li + li::before {
            padding: 0;
            content: " » ";
        }
    </style>
@stop

@section('js')
<script src="/assets/js/app/common.js" charset="UTF-8"></script>
<script type="text/javascript" src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
    <script>
        laydate.skin('lynn');
        var layConfig ={
            elem: '#created_start_date',
            event: 'focus',
            format: 'YYYY-MM-DD hh:mm:ss',
            istime: true,
        };
        laydate(layConfig);

        layConfig.elem = '#created_end_date';
        laydate(layConfig);
        layConfig.elem = '#frozen_start_date';
        laydate(layConfig);
        layConfig.elem = '#frozen_end_date';
        laydate(layConfig);

        $(function () {
        	var get_params = function (data) {
                var param = {
                        'id'             		: $("input[name='id']").val(),
                        'username'       		: $("input[name='username']").val(),
                        'include_all'           : $("input[name='include_all']:checked").val(),
                        'sub_recharge_status'           : $("input[name='sub_recharge_status']:checked").val(),
                        'user_type_id'  		: $("select[name='user_type_id']").val(),
                        'user_group_id'  		: $("select[name='user_group_id']").val(),
                        'created_start_date'	: $("input[name='created_start_date']").val(),
                        'created_end_date'		: $("input[name='created_end_date']").val(),
                        'frozen_start_date' 	: $("input[name='frozen_start_date']").val(),
                        'frozen_end_date'    	: $("input[name='frozen_end_date']").val(),
                        'balance_min'   		: $('input[name="balance_min"]').val(),
                        'balance_max'        	: $('input[name="balance_max"]').val(),
                        'is_search'       		: $("input[name='is_search']").val(),
                    };
                    return $.extend({}, data, param);
        	}

        	var json_cb = function(json) {
        		if(typeof(json.parent_tree) !== undefined) {
        			showParentTree(json.parent_tree);
        		} else {
        			showParentTree(null);
        		}
        	}

            var table = $("#tags-table").DataTable({
            	language:app.DataTable.language(),
                order: [[1, "asc"]],
                serverSide: true,
                pageLength:25,
                searching:false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type, get_params) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params, json_cb),
                "columns": [
                    {"visible":false},
                	{"data": "user_id"},
                	{"data": "username"},
                    {"data": "team_count"},
                    {"data": "user_level"},
                    {"data": "user_group_name"},
                	{"data": "lottery_rebate"},
                	{"data": "balance"},
                	{"data": "hold_balance"},
                	{"data": "sub_recharge_status"},
                	{"data": "created_at"},
                    {"data": "last_time"},
                    {"data": "frozen"},
                    {"data": "online_status"},
                    {"data": null},
                ],
                 createdRow: function (row, data, index) {
                    if (data['frozen'] == '1') {
                        $(row).addClass('danger');
                    }else if (data['frozen'] == '2') {
                        $(row).addClass('info');
                    }else if (data['frozen'] == '3') {
                         $(row).addClass('warning');
                     }
                },
                columnDefs: [
                        {
                            'targets':2,
                            "render": function (data, type, row) {
                                if (typeof row.self !== 'undefined' && row.self === 1) {
                                    if (row['user_observe']) {
                                        return app.getColorHtml(row.username, 'label-danger', false);
                                    } else {
                                        return row.username;
                                    }
                                } else {
                                    if (row['user_observe']) {
                                        return '<a href="/user/?id=' + row.user_id + '" class="label-danger">' + row.username + '</a>';
                                    }else{
                                        return '<a href="/user/?id=' + row.user_id + '" >' + row.username + '</a>';
                                    }
                                }
                            }
                        },
                        {
                            'targets': 4,
                            'render': function (data, type, row) {
                                return app.getLabelHtml(data, 'label-primary');
                            }
                        },
                        {
                            'targets': 5,
                            'render': function (data, type, row) {
                                var label = 'label-success';

                                if (row.user_group_id == 2 ) {
                                	label = 'label-warning';
                                } else if (row.user_group_id == 3 ) {
                                	label = 'label-danger';
                                }

                                return app.getLabelHtml(data, label);
                            }
                        },
                        {
                            'targets': -6,
                            'render': function (data, type, row) {

                                return '<a style="font-weight: bold" href="javascript:;" user-id="'+row.user_id+'" ' +
                                    'class="label sub_recharge '+(row.sub_recharge_status == 0?' label-warning':' label-success')+'"> ' +
                                    (row.sub_recharge_status == 0?'无':(row.sub_recharge_status == 1?'直属':'所有'))+
                                    '</a>';
                            }
                        },
                        {
                            'targets': -3,
                            'render': function (data, type, row) {
                                var label = 'label-success';

                                if (row.frozen > 0 ) {
                                    label = row.frozen==1?'label-danger':'label-warning';
                                    return app.getLabelHtml('冻结', label);
                                }else{
                                    if(row.user_observe){
                                        label = 'label-primary';
                                        return app.getLabelHtml('观察', label);
                                    }else {
                                        label = 'label-success';
                                        return app.getLabelHtml('正常', label);
                                    }
                                }
                            }
                        },
                        {
                            'targets': -2,
                            'render': function (data, type, row) {

                            return '<span user-id="'+row.user_id+'" class="'+(row.online_status == '离线'?'text-gray':'kickout text-success')+'" title="'+(row.online_status == '离线'?'':'点击踢用户下线')+'"><i class="fa fa-circle text-'+(row.online_status == '离线'?'gray ':'success')+'"></i> '+(row.online_status == '离线'?'离线':'在线')+'</span>';
                            }
                        },
                        {
                            'targets': -1,
                            "render": function (data, type, row) {
                                var str = '';
                                str += '<a mounttabs="" title="用户详情['+row.username +']" href="/user/detail/?id=' + row.user_id + '" class="X-Small btn-xs text-primary "><i class="fa fa-search" aria-hidden="true"></i>详情</a>';
                                str += '<a mounttabs="" title="用户银行卡['+row.username +']" href="/user/banks/?id=' + row.user_id + '" class="X-Small btn-xs text-primary ">银行卡</a>';
                                if(row['frozen']==0){
                                    str += '<a mounttabs="" title="冻结用户['+row.username +']" href="/user/freeze/?id=' + row.user_id + '" class="X-Small btn-xs text-primary ">冻结</a>';
                                }else{
                                    str += '<a mounttabs="" title="解冻用户['+row.username +']" href="/user/freeze/?id=' + row.user_id + '" class="X-Small btn-xs text-danger ">解冻</a>';
                                }
                                str += '<a mounttabs="" title="用户充值['+row.username +']" href="/user/recharge/?id=' + row.user_id + '" class="X-Small btn-xs text-primary ">充值</a>';
                                str += '<a mounttabs="" title="用户扣款['+row.username +']" href="/user/deduct/?id=' + row.user_id + '" class="X-Small btn-xs text-primary ">扣款</a>';
                                str += '<a mounttabs="" title="修改密码用户['+row.username +']" href="/user/changepass/?id=' + row.user_id + '" class="X-Small btn-xs text-primary ">密码</a>';
                                @if(Gate::check('user/unbindtelephone'))
                                if(row.telephone){
                                    str += '<a  title="解绑手机用户['+row.username +']" href="JavaScript:;" data="'+row.user_id+'" username="'+row.username+'" class="X-Small btn-xs text-primary unbindTelephone">解绑手机</a>';
                                }
                                @endif
                                str += '<a href="#" class="more_links X-Small btn-xs text-primary">更多...</a>'+
                                    '<div class="pop_links" style="display: none;">'+

                                    @if(get_config('dailywage_available') == 1 && Gate::check('userdailywagecontract/index'))
                                        '<a mounttabs="" title="工资用户['+row.username +']" href="/userdailywagecontract/?username=' + row.username + '" class="X-Small btn-xs text-primary " mounttabs title="日工资契约列表">工资</a>' +
                                    @endif

                                    '<a mounttabs="" title="密保用户['+row.username +']" href="/user/securityquestion/?id=' + row.user_id + '" class="X-Small btn-xs text-primary ">密保</a>'+
                                    '<a mounttabs="" title="返点用户['+row.username +']" href="/user/Rebates/?id=' + row.user_id + '" class="X-Small btn-xs text-primary ">返点返水</a>'+
                                    '<a mounttabs="" title="配额用户['+row.username +']" href="/user/quota/?id=' + row.user_id + '" class="X-Small btn-xs text-primary ">配额管理</a>'+
                                    '<a mounttabs="" title="账变用户['+row.username +']" href="/order/?username=' + row.username + '" target="_blank" class="X-Small btn-xs text-primary ">帐变</a>';
                                if(row['google_key']){
                                    str += '<a href="javascript:;" attr="'+row.user_id+'" class="X-Small btn-xs text-primary google_key">登陆器解绑</a>';
                                }

                                @if(get_config('dividend_available') == 1 )
                                    @if(Gate::check('dividend/createoredit'))
                                        str += '<a mounttabs="" title="契约分红用户['+row.username +']" href="/dividend/createoredit?user_id=' + row.user_id + '" title="契约分红" class="X-Small btn-xs text-primary dividend" mounttabs>契约分红</a>';
                                    @endif
                                    @if(Gate::check('user/dividendlock'))
                                        if( row['dividend_lock'] == 1){
                                            str += '<a href="javascript:;" attr="'+row.user_id+'"  type="unlock" title="解锁[分红锁]" class="X-Small btn-xs text-primary dividend_lock" >解锁[分红锁]</a>';
                                        }else if( row['dividend_lock'] == -1){
                                            str += '<a href="javascript:;" attr="'+row.user_id+'"  type="lock" title="锁定[分红锁]" class="X-Small btn-xs text-primary dividend_lock" >锁定[分红锁]</a>';
                                        }
                                    @endif
                                @endif

                                @if(Gate::check('user/prizelevel'))
                                if(row['user_type_id'] == 1) {
                                    str += '<a mounttabs="" title="奖级调整用户['+row.username +']" href="/user/prizelevel?user_id=' + row.user_id + '" title="总代奖级调整" class="X-Small btn-xs text-primary prizelevel" mounttabs>奖级调整</a>';
                                }
                                @endif
                                    
                                @if(Gate::check('user/changetoagent'))
                                    if(row['user_type_id'] == 3) {
                                        str += '<a href="javascript:;" attr="' + row.user_id + '" class="X-Small btn-xs text-primary change_to_agent">身份变更</a>';
                                    }
                                @endif

                                @if(Gate::check('user/recythirdbalance'))
                                    if (row['user_group_id'] == 1) {
                                        str += '<a href="javascript:;" attr="' + row.user_id + '" class="X-Small btn-xs text-primary recy_third_balance">收回余额</a>';
                                    }
                                @endif

                                str += '<a href="javascript:;" attr="'+row.user_id+'" class="X-Small btn-xs text-primary del">删除</a>'+
                                    '<a mounttabs="" title="积分用户['+row.username +']" href="/user/points/?id=' + row.user_id + '" class="X-Small btn-xs text-primary ">积分</a>'+
                                    '<a mounttabs="" title="提款次数用户['+row.username +']" href="/user/withdrawallimit/?id=' + row.user_id + '" class="X-Small btn-xs text-primary ">提款次数</a>'+
                                    '<a mounttabs="" title="站内信用户['+row.username +']" href="/user/sendmsg/?id=' + row.user_id + '" class="X-Small btn-xs text-primary ">站内信</a>'+
                                    '<a mounttabs="" title="充值白名单用户['+row.username +']" href="/user/depositwhitelist/?id=' + row.user_id + '" class="X-Small btn-xs text-'+(row.is_pay_whitelist?'success':'danger')+' ">充值白名单</a>'+
                                @if(Gate::check('user/userlevel'))
                                    '<a href="javascript:;" attr="'+row.user_id+'" class="X-Small btn-xs text-primary user_level">用户分层</a>'+
                                @endif
                                @if(Gate::check('user/userobserve'))
                                    '<a href="javascript:;" attr="' + row.user_id + '" class="X-Small btn-xs text-primary user_observe">重点观察</a>' +
                                @endif
                                @if(Gate::check('user/setadduser'))
                                    '<a href="javascript:;" attr="' + row.user_id + '" class="X-Small btn-xs text-'+(row.ban_add_user?'danger':'primary')+' setadduser">开户权限</a>' +
                                @endif
                                @if(Gate::check('user/adduser'))
                                    '<a mounttabs="" title="添加下级['+row.username +']" href="/user/adduser/?id=' + row.user_id + '" class="X-Small btn-xs text-primary ">添加下级</a>'+
                                @endif
                                @if(Gate::check('user/issuelimitbet'))
                                    '<a href="javascript:;" attr="' + row.user_id + '" class="X-Small btn-xs text-primary issuelimitbet">单期限额</a>' +
                                @endif
                                @if(Gate::check('user/bantransfer'))
                                    '<a href="javascript:;" attr="' + row.user_id + '" class="X-Small btn-xs text-primary bantransfer">转账权限</a>' +
                                @endif
                                @if(Gate::check('user/banwithdrawal'))
                                    '<a href="javascript:;" attr="' + row.user_id + '" class="X-Small btn-xs text-primary banwithdrawal">提款权限</a>' +
                                @endif
                                @if(Gate::check('user/adduserlimit'))
                                    '<a href="javascript:;" attr="' + row.user_id + '" class="X-Small btn-xs text-primary adduserlimit">开户限额</a>' +
                                @endif
                                @if(Gate::check('user/skip_diff_ip_verify'))
                                    '<a href="javascript:;" attr="' + row.user_id + '" class="X-Small btn-xs text-primary skip_diff_ip_verify">异地登录</a>' +
                                @endif
                                @if(Gate::check('user/allowtransfertoparent'))
                                    '<a href="javascript:;" attr="' + row.user_id + '" class="X-Small btn-xs text-primary transfer_to_parent">向上级转账</a>' +
                                @endif
                                @if(get_config('private_return_enabled') == 1)
                                    @if(Gate::check('user/userprivatereturn'))
                                        '<a href="javascript:;" attr="' + row.user_id + '" class="X-Small btn-xs text-primary user_private_return">私返权限</a>' +
                                    @endif
                                    @if(Gate::check('userprivatereturn/index'))
                                        '<a mounttabs="" title="私返用户['+row.username +']" href="/userprivatereturn/?username=' + row.username + '" class="X-Small btn-xs text-primary " mounttabs title="私返契约列表">私返契约</a>' +
                                    @endif
                                @endif
                                
                                '</div>';
                                return str;
                            }
                        }
                ],
                drawCallback: function () {
                    $('.more_links').popover({
                        trigger: "manual" ,
                        html: true,
                        animation:false,
                        placement: 'auto right',
                        container: 'body',
                        content: function () {
                            return  $(this).siblings(".pop_links").html();
                        }
                    }).on("mouseenter", function () {
                        var _this = this;
                        $(this).popover("show");
                        $(".popover").on("mouseleave", function () {
                            $(_this).popover('hide');
                        });
                    }).on("mouseleave", function () {
                        var _this = this;
                        setTimeout(function () {
                            if (!$(".popover:hover").length) {
                                $(_this).popover("hide");
                            }
                        }, 100);
                    }).click(function(e) {
                        e.preventDefault();
                    })
                },
            });

            table.on('preXhr.dt', function () {
                loadShow();
            });

            table.on('draw.dt', function () {
                var data = table.row(0).data() || {};
                if (typeof data.self !== 'undefined' && data.self === 1) {
                    $(table.row(0).node()).css('background-color', '#ffece6');
                }

                loadFadeOut();
            });

            $('#search').submit(function(event){
            	event.preventDefault();
            	$("input[name='is_search']").val(1);
                table.ajax.reload();
            });

            $.fn.dataTable.ext.errMode = function(){
                app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
                loadFadeOut();
            };

            $('#reload_table').click(function(){
                table.draw(false);
            });
            $(document).on( 'click',".unbindTelephone", function () {
                var user_id = $(this).attr('data');
                var username = $(this).attr('username');
                BootstrapDialog.confirm({
                    message: '确定要解绑'+username+'吗？',
                    type: BootstrapDialog.TYPE_WARNING,
                    closable: true,
                    draggable: true,
                    btnCancelLabel: '取消',
                    btnOKLabel: '解绑',
                    btnOKClass: 'btn-warning',
                    callback: function(result) {
                        if(result) {
                            $.ajax({
                                url: "/user/unbindtelephone",
                                dataType: "json",
                                method: "POST",
                                data:{user_id:user_id}
                            }).done(function (json) {
                                if(json.status==0){
                                    table.draw(false);
                                    notify(json.msg,'success');
                                }else{
                                    notify(json.msg,'error');
                                }

                            }).error(function (jqXHR, textStatus, errorThrown) {
                                alert(errorThrown.toString())
                            });
                        }
                    }
                });
            });
            function showParentTree(data) {
    	            var str = '';
    	            if(data) {
    	                for(var i=0; i<data.length; i++) {
    	                    str += '<li> <a href="/user/?id=' + data[i].user_id + '" class="X-Small btn-xs text-maroon">'+ data[i].username +'</a></li>';
    	                }
    	            }
    	            $breadcrumb = $("#parent_tree");
    	            $breadcrumb.children().remove();
                if(str==''){
                    $breadcrumb.hide();
                }else{
                    $breadcrumb.append(str);
                    $breadcrumb.show();
                }
    	    }
             $(document).on( 'click',".del", function () {
                    var id = $(this).attr('attr');
                 BootstrapDialog.confirm('确认要删除用户吗?', function(result){
                     if(result) {
                         $.ajax({
                             url: "/user/delete",
                             dataType: "json",
                             method: "POST",
                             data:{id:id},
                         }).done(function (json) {
                             var type='danger';
                             if (json.status == 0) {
                                 type = 'success'
                                 table.ajax.reload();
                             }
                             $.notify({
                                 title: '<strong>提示!</strong>',
                                 message: json.msg
                             },{
                                 type: type
                             });
                         });
                     }
                 });
                });
            $(document).on( 'click',".google_key", function () {
                var id = $(this).attr('attr');
                BootstrapDialog.confirm('确认要清楚用户谷歌绑定?', function(result){
                    if(result) {
                        $.ajax({
                            url: "/user/googlekey",
                            dataType: "json",
                            method: "POST",
                            data:{user_id:id},
                        }).done(function (json) {
                            var type='danger';
                            if (json.status == 0) {
                                type = 'success'
                                table.ajax.reload();
                            }
                            $.notify({
                                title: '<strong>提示!</strong>',
                                message: json.msg
                            },{
                                type: type
                            });
                        });
                    }
                });
            });
            //用户分层功能
            $(document).on( 'click',".user_level", function () {
                var id = $(this).attr('attr');
                BootstrapDialog.show({
                    title:'设置用户分层',
                    message: $('<div></div>').load("/user/userlevel?id=" + id),
                    buttons: [{
                        icon: 'glyphicon glyphicon-send',
                        label: '保存设置',
                        cssClass: 'btn-primary',
                        autospin: true,
                        action: function(dialogRef){
                            dialogRef.enableButtons(false);
                            dialogRef.setClosable(false);
                            $.ajax({
                                url: "/user/userlevel",
                                dataType: "json",
                                method: "POST",
                                data:$("#userlevel-form").serialize(),
                            }).done(function (json) {
                                dialogRef.close();
                                var type='danger';
                                if (json.status == 0) {
                                    type = 'success'
                                    table.ajax.reload();
                                }
                                $.notify({
                                    title: '<strong>提示!</strong>',
                                    message: json.msg
                                },{
                                    type: type
                                });
                            });
                        }
                    }, {
                        label: '取消',
                        action: function(dialogRef){
                            dialogRef.close();
                        }
                    }]
                });
            });

            //分红锁
            $(document).on( 'click',".dividend_lock", function () {
                var type = $(this).attr('type');
                var user_id = $(this).attr('attr');
                BootstrapDialog.confirm({
                    message: (type=='lock')?'确认锁定用户？锁定用户可登陆，不可提现，不可投注[彩票，VR]，不可转账[上下级，第三方]':'确认解锁用户？',
                    type: BootstrapDialog.TYPE_WARNING,
                    closable: true,
                    draggable: true,
                    btnCancelLabel: '取消',
                    btnOKLabel: '确认提交',
                    btnOKClass: 'btn-warning',
                    callback: function(result) {
                        if (result) {
                            loadShow();
                            $.ajax({
                                url: "/user/dividendLock",
                                dataType: "json",
                                method: "PUT",
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                                data:{
                                    'user_id':user_id,
                                    'type':type,
                                },
                            }).done(function (json) {
                                loadFadeOut();
                                if (json.hasOwnProperty('code') && json.code == '302') {
                                    window.location.reload();
                                }
                                var notify_type = 'danger';
                                if (json.status == 0) {
                                    notify_type = 'success';
                                    table.ajax.reload();
                                }
                                $.notify({
                                    title: '<strong>提示!</strong>',
                                    message: json.msg
                                },{
                                    type: notify_type
                                });
                            });
                        }
                    }});
            });
            //权限设置
            $(document).on( 'click',".bantransfer", function () {
                var id = $(this).attr('attr');
                BootstrapDialog.show({
                    title:'设置权限',
                    message: $('<div></div>').load("/user/bantransfer?id=" + id),
                    buttons: [{
                        icon: 'glyphicon glyphicon-send',
                        label: '保存设置',
                        cssClass: 'btn-primary',
                        autospin: true,
                        action: function(dialogRef){
                            dialogRef.enableButtons(false);
                            dialogRef.setClosable(false);
                            $.ajax({
                                url: "/user/bantransfer",
                                dataType: "json",
                                method: "POST",
                                data:$("#bantransfer-form").serialize(),
                            }).done(function (json) {
                                dialogRef.close();
                                var type='danger';
                                if (json.status == 0) {
                                    type = 'success'
                                    table.ajax.reload();
                                }
                                $.notify({
                                    title: '<strong>提示!</strong>',
                                    message: json.msg
                                },{
                                    type: type
                                });
                            });
                        }
                    }, {
                        label: '取消',
                        action: function(dialogRef){
                            dialogRef.close();
                        }
                    }]
                });
            });
            //设置开户权限
            $(document).on( 'click',".setadduser", function () {
                var id = $(this).attr('attr');
                BootstrapDialog.show({
                    title:'设置开户权限',
                    message: $('<div></div>').load("/user/setadduser?id=" + id),
                    buttons: [{
                        icon: 'glyphicon glyphicon-send',
                        label: '保存设置',
                        cssClass: 'btn-primary',
                        autospin: true,
                        action: function(dialogRef){
                            dialogRef.enableButtons(false);
                            dialogRef.setClosable(false);
                            $.ajax({
                                url: "/user/setadduser",
                                dataType: "json",
                                method: "POST",
                                data:$("#setadduser-form").serialize(),
                            }).done(function (json) {
                                dialogRef.close();
                                var type='danger';
                                if (json.status == 0) {
                                    type = 'success'
                                    table.ajax.reload();
                                }
                                $.notify({
                                    title: '<strong>提示!</strong>',
                                    message: json.msg
                                },{
                                    type: type
                                });
                            });
                        }
                    }, {
                        label: '取消',
                        action: function(dialogRef){
                            dialogRef.close();
                        }
                    }]
                });
            });
            //设置重点观察用户
            $(document).on( 'click',".user_observe", function () {
                var id = $(this).attr('attr');
                BootstrapDialog.show({
                    title:'设置重点观察用户',
                    message: $('<div></div>').load("/user/userobserve?id=" + id),
                    buttons: [{
                        icon: 'glyphicon glyphicon-send',
                        label: '保存设置',
                        cssClass: 'btn-primary',
                        autospin: true,
                        action: function(dialogRef){
                            var uesr_id = $('#user_observe_userid').val();
                            var status = $('#user_observe_status').prop('checked');
                            var comment = $('#user_observe_comment').val().trim();
                            if (status == true && comment == '') {
                                app.bootoast("请输入备注");
                                return false;
                            }
                            dialogRef.enableButtons(false);
                            dialogRef.setClosable(false);
                            $.ajax({
                                url: "/user/userobserve",
                                dataType: "json",
                                method: "POST",
                                data:$("#userobserve-form").serialize(),
                            }).done(function (json) {
                                dialogRef.close();
                                var type='danger';
                                if (json.status == 0) {
                                    type = 'success'
                                    table.ajax.reload();
                                }
                                $.notify({
                                    title: '<strong>提示!</strong>',
                                    message: json.msg
                                },{
                                    type: type
                                });
                            });
                        }
                    }, {
                        label: '取消',
                        action: function(dialogRef){
                            dialogRef.close();
                        }
                    }]
                });
            });
            //设置开户权限
            $(document).on( 'click',".issuelimitbet", function () {
                var id = $(this).attr('attr');
                BootstrapDialog.show({
                    title:'设置用户单期限额',
                    message: $('<div></div>').load("/user/issuelimitbet?id=" + id),
                    buttons: [{
                        icon: 'glyphicon glyphicon-send',
                        label: '保存设置',
                        cssClass: 'btn-primary',
                        autospin: true,
                        action: function(dialogRef){
                            dialogRef.enableButtons(false);
                            dialogRef.setClosable(false);
                            $.ajax({
                                url: "/user/issuelimitbet",
                                dataType: "json",
                                method: "POST",
                                data:$("#issuelimitbet-form").serialize(),
                            }).done(function (json) {
                                dialogRef.close();
                                var type='danger';
                                if (json.status == 0) {
                                    type = 'success'
                                    table.ajax.reload();
                                }
                                $.notify({
                                    title: '<strong>提示!</strong>',
                                    message: json.msg
                                },{
                                    type: type
                                });
                            });
                        }
                    }, {
                        label: '取消',
                        action: function(dialogRef){
                            dialogRef.close();
                        }
                    }]
                });
            });
            //设置用户提款权限
            $(document).on( 'click',".banwithdrawal", function () {
                var id = $(this).attr('attr');
                BootstrapDialog.show({
                    title:'设置用户提款权限',
                    message: $('<div></div>').load("/user/banwithdrawal?id=" + id),
                    buttons: [{
                        icon: 'glyphicon glyphicon-send',
                        label: '保存设置',
                        cssClass: 'btn-primary',
                        autospin: true,
                        action: function(dialogRef){
                            dialogRef.enableButtons(false);
                            dialogRef.setClosable(false);
                            $.ajax({
                                url: "/user/banwithdrawal",
                                dataType: "json",
                                method: "POST",
                                data:$("#banwithdrawal-form").serialize(),
                            }).done(function (json) {
                                dialogRef.close();
                                var type='danger';
                                if (json.status == 0) {
                                    type = 'success'
                                    table.ajax.reload();
                                }
                                $.notify({
                                    title: '<strong>提示!</strong>',
                                    message: json.msg
                                },{
                                    type: type
                                });
                            });
                        }
                    }, {
                        label: '取消',
                        action: function(dialogRef){
                            dialogRef.close();
                        }
                    }]
                });
            });
            $('#create_shiwan').click(function(){

                loadShow();
                $.ajax({
                    url: "/user/createshiwan",
                    dataType: "json",
                    method: "POST",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                }).done(function (json) {
                    loadFadeOut();
                    if (json.hasOwnProperty('code') && json.code == '302') {
                        window.location.reload();
                    }
                    var type='danger';
                    if (json.status == 0) {
                        type = 'success'
                    }
                    $.notify({
                        title: '<strong>提示!</strong>',
                        message: json.msg
                    },{
                        type: type
                    });
                });
            });
            $(document).on( 'click',".kickout", function () {
                var user_id = $(this).attr('user-id');
                BootstrapDialog.confirm('确认要踢用户下线吗?', function(result){
                    if(result) {
                        $.ajax({
                            url: "/user/kickOut",
                            dataType: "json",
                            method: "POST",
                            data:{id:user_id},
                        }).done(function (json) {
                            var type='danger';
                            if (json.status == 0) {
                                type = 'success'
                                table.ajax.reload();
                            }
                            $.notify({
                                title: '<strong>提示!</strong>',
                                message: json.msg
                            },{
                                type: type
                            });
                        });
                    }
                });
            });
            $(document).on( 'click',".sub_recharge", function () {
                var id = $(this).attr('user-id');
                BootstrapDialog.show({
                    title:'设置用户下级充值权限',
                    message: $('<div></div>').load("/user/subRechargewhitelist/?id=" + id),
                    buttons: [{
                        icon: 'glyphicon glyphicon-send',
                        label: '保存设置',
                        cssClass: 'btn-primary',
                        autospin: true,
                        action: function(dialogRef){
                            dialogRef.enableButtons(false);
                            dialogRef.setClosable(false);
                            $.ajax({
                                url: "/user/subRechargewhitelist/?id=" + id,
                                dataType: "json",
                                method: "PUT",
                                data:$("#subrecharge-form").serialize(),
                            }).done(function (json) {
                                dialogRef.close();
                                var type='danger';
                                if (json.status == 0) {
                                    type = 'success'
                                    table.ajax.reload();
                                }
                                $.notify({
                                    title: '<strong>提示!</strong>',
                                    message: json.msg
                                },{
                                    type: type
                                });
                            });
                        }
                    }, {
                        label: '取消',
                        action: function(dialogRef){
                            dialogRef.close();
                        }
                    }]
                });
            });
            //设置用户开户限额
            $(document).on( 'click',".adduserlimit", function () {
                var id = $(this).attr('attr');
                BootstrapDialog.show({
                    title:'设置用户开户限额',
                    message: $('<div></div>').load("/user/adduserlimit?id=" + id),
                    buttons: [{
                        icon: 'glyphicon glyphicon-send',
                        label: '保存设置',
                        cssClass: 'btn-primary',
                        autospin: true,
                        action: function(dialogRef){
                            dialogRef.enableButtons(false);
                            dialogRef.setClosable(false);
                            $.ajax({
                                url: "/user/adduserlimit",
                                dataType: "json",
                                method: "POST",
                                data:$("#adduserlimit-form").serialize(),
                            }).done(function (json) {
                                dialogRef.close();
                                var type='danger';
                                if (json.status == 0) {
                                    type = 'success'
                                    table.ajax.reload();
                                }
                                $.notify({
                                    title: '<strong>提示!</strong>',
                                    message: json.msg
                                },{
                                    type: type
                                });
                            });
                        }
                    }, {
                        label: '取消',
                        action: function(dialogRef){
                            dialogRef.close();
                        }
                    }]
                });
            });
            //异地登录设置
            $(document).on( 'click',".skip_diff_ip_verify", function () {
                var id = $(this).attr('attr');
                BootstrapDialog.show({
                    title:'异地登录设置',
                    message: $('<div></div>').load("/user/skip_diff_ip_verify?id=" + id),
                    buttons: [{
                        icon: 'glyphicon glyphicon-send',
                        label: '保存设置',
                        cssClass: 'btn-primary',
                        autospin: true,
                        action: function(dialogRef){
                            dialogRef.enableButtons(false);
                            dialogRef.setClosable(false);
                            $.ajax({
                                url: "/user/skip_diff_ip_verify",
                                dataType: "json",
                                method: "POST",
                                data:$("#skip_diff_ip_verify-form").serialize(),
                            }).done(function (json) {
                                dialogRef.close();
                                var type='danger';
                                if (json.status == 0) {
                                    type = 'success'
                                    table.ajax.reload();
                                }
                                $.notify({
                                    title: '<strong>提示!</strong>',
                                    message: json.msg
                                },{
                                    type: type
                                });
                            });
                        }
                    }, {
                        label: '取消',
                        action: function(dialogRef){
                            dialogRef.close();
                        }
                    }]
                });
            });

            //向上级转账设置
            $(document).on( 'click',".transfer_to_parent", function () {
                var id = $(this).attr('attr');
                BootstrapDialog.show({
                    title:'向上级转账设置',
                    message: $('<div></div>').load("/user/AllowTransferToParent?id=" + id),
                    buttons: [{
                        icon: 'glyphicon glyphicon-send',
                        label: '保存设置',
                        cssClass: 'btn-primary',
                        autospin: true,
                        action: function(dialogRef){
                            dialogRef.enableButtons(false);
                            dialogRef.setClosable(false);
                            $.ajax({
                                url: "/user/AllowTransferToParent",
                                dataType: "json",
                                method: "POST",
                                data:$("#transfer-to-parent-form").serialize(),
                            }).done(function (json) {
                                dialogRef.close();
                                var type='danger';
                                if (json.status == 0) {
                                    type = 'success'
                                    table.ajax.reload();
                                }
                                $.notify({
                                    title: '<strong>提示!</strong>',
                                    message: json.msg
                                },{
                                    type: type
                                });
                            });
                        }
                    }, {
                        label: '取消',
                        action: function(dialogRef){
                            dialogRef.close();
                        }
                    }]
                });
            });

            //会员身分转为代理(身分变更)
            $(document).on( 'click',".change_to_agent", function () {
                var id = $(this).attr('attr');
                BootstrapDialog.show({
                    title:'身份变更',
                    message: $('<div></div>').load("/user/changetoagent?id=" + id),
                    buttons: [{
                        icon: 'glyphicon glyphicon-send',
                        label: '保存设置',
                        cssClass: 'btn-primary',
                        autospin: true,
                        action: function(dialogRef){
                            dialogRef.enableButtons(false);
                            dialogRef.setClosable(false);
                            BootstrapDialog.confirm('转移后无法恢复成会员，确认是否将会员身份转为代理？', function(result){
                                if (result) {
                                    $.ajax({
                                        url: "/user/changetoagent",
                                        dataType: "json",
                                        method: "POST",
                                        data:$("#change-to-agent-form").serialize(),
                                    }).done(function (json) {
                                        dialogRef.close();
                                        var type='danger';
                                        if (json.status == 0) {
                                            type = 'success'
                                            table.ajax.reload();
                                        }
                                        $.notify({
                                            title: '<strong>提示!</strong>',
                                            message: json.msg
                                        },{
                                            type: type
                                        });
                                    });
                                } else {
                                    dialogRef.close();
                                }
                            });
                        }
                    }, {
                        label: '取消',
                        action: function(dialogRef){
                            dialogRef.close();
                        }
                    }]
                });
            });

            //私返设置
            $(document).on( 'click',".user_private_return", function () {
                var id = $(this).attr('attr');
                BootstrapDialog.show({
                    title:'私返设置',
                    message: $('<div></div>').load("/user/UserPrivateReturn?id=" + id),
                    buttons: [{
                        icon: 'glyphicon glyphicon-send',
                        label: '保存设置',
                        cssClass: 'btn-primary',
                        autospin: true,
                        action: function(dialogRef){
                            dialogRef.enableButtons(false);
                            dialogRef.setClosable(false);
                            $.ajax({
                                url: "/user/UserPrivateReturn",
                                dataType: "json",
                                method: "POST",
                                data:$("#private-return-form").serialize(),
                            }).done(function (json) {
                                dialogRef.close();
                                var type='danger';
                                if (json.status == 0) {
                                    type = 'success'
                                    table.ajax.reload();
                                }
                                $.notify({
                                    title: '<strong>提示!</strong>',
                                    message: json.msg
                                },{
                                    type: type
                                });
                            });
                        }
                    }, {
                        label: '取消',
                        action: function(dialogRef){
                            dialogRef.close();
                        }
                    }]
                });
            });

            //收回三方余额
            $(document).on( 'click',".recy_third_balance", function () {
                loadShow();
                $.ajax({
                    url: "/user/RecyThirdBalance?id=" +  $(this).attr('attr'),
                    dataType: "json",
                    method: "POST",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
                }).done(function (json) {
                    loadFadeOut();
                    if (json.hasOwnProperty('code') && json.code == '302') {
                        window.location.reload();
                    }
                    var type = 'danger';
                    if (json.status == 0) {
                        type = 'success'
                        table.ajax.reload();
                    }
                    $.notify({
                        title: '<strong>提示!</strong>',
                        message: json.msg
                    }, {
                        type: type
                    });
                });
            });
        });

    </script>
@stop

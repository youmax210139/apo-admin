@extends('layouts.base')

@section('title','会话管理')

@section('function','会话管理')
@section('function_link', '/chatdeposit/')

@section('here','会话列表')

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <!--搜索框 Start-->
            <div class="box box-primary">
                <form id="search" class="form-horizontal" action="/chatdeposit/" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="export" value="1"/>
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
                                <label class="col-sm-3 control-label">会话时间</label>
                                <div class="col-sm-9">
                                    <div class="input-daterange input-group">
                                        <input type="text" class="form-control form_datetime" name="start_date" value="" id='start_date' placeholder="开始时间">
                                        <span class="input-group-addon">~</span>
                                        <input type="text" class="form-control form_datetime" name="end_date" value="" id='end_date' placeholder="结束时间">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group search_username">
                                <label class="col-sm-3 control-label">用户名</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="username" placeholder="用户名">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">专员</label>
                                <div class="col-sm-9">
                                    <select name="kefu" class="form-control">
                                        <option value="0">所有专员</option>
                                        <option value="1">专员1</option>
                                        <option value="2">专员2</option>
                                        <option value="3">专员3</option>
                                        <option value="4">专员4</option>
                                        <option value="5">专员5</option>
                                        <option value="6">专员6</option>
                                        <option value="7">专员7</option>
                                        <option value="8">专员8</option>
                                        <option value="9">专员9</option>
                                        <option value="10">专员10</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer text-center">
                        <button type="submit" class="btn btn-primary margin" id="search_btn"><i class="fa fa-search" aria-hidden="true"></i>查询</button>
                        <button type="reset" class="btn btn-default margin"><i class="fa fa-refresh" aria-hidden="true"></i>重置</button>
                    </div>
                </form>
            </div>
            <!--搜索框 End-->
            <div class="row">
                <div class="col-sm-6 checkbox">
                    <label>
                        <input type="checkbox" name="auto_refresh" checked>
                        <select name="refresh_delay">
                            <option value="10" selected>10</option>
                            <option value="15">15</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="60">60</option>
                        </select>秒自动刷新
                        <audio id="mediaElementID" style="height: 10px" src="/assets/sound/message.mp3" preload="auto"
                               controls></audio>
                    </label>
                </div>
                <div class="col-sm-6 text-right">
                    @if(Gate::check("chatdeposit/payment"))
                    <a href="#" onclick="setConfig('bank')" class="btn btn-primary btn-md"><i class="fa fa-cog"></i> 设置收款账号 </a>
                        <a href="#" onclick="addConfig()" class="btn btn-primary btn-md"><i class="fa fa-plus-circle"></i> 添加账号 </a>
                    @endif
                        @if(Gate::check("chatdeposit/autokeyword"))
                            <a href="#" onclick="autoKeyword()" class="btn btn-warning btn-md"><i class="fa fa-soundcloud"></i> 自动回复语 </a>
                        @endif
                </div>
            </div>
            <div class="box box-primary">
                <style>
                    table.dataTable.table-condensed tr > th {
                        text-align: center;
                        vertical-align: middle;
                    }

                    table.dataTable.table-condensed tbody > tr > td {
                        text-align: center;
                        vertical-align: middle;
                    }
                </style>
                <div class="box-body">
                    @include('partials.errors')
                    @include('partials.success')
                    <table id="tags-table" class="table table-striped table-condensed table-bordered table-hover app_w100pct dataTable no-footer">
                        <thead>
                            <tr>
                                <th class="hidden-sm" data-sortable="false"></th>
                                <th class="hidden-sm" data-sortable="false">ID</th>
                                <th class="hidden-sm" data-sortable="false">充值订单ID</th>
                                <th class="hidden-sm" data-sortable="false">通道</th>
                                <th class="hidden-sm" data-sortable="false">用户名</th>
                                <th class="hidden-sm" >充值金额</th>
                                <th class="hidden-sm" >充值状态</th>
                                <th class="hidden-sm" >最新消息</th>
                                <th class="hidden-sm" >状态</th>
                                <th class="hidden-sm" >发送时间</th>
                                <th class="hidden-sm" data-sortable="false">专员/前台名称</th>
                                <th class="hidden-sm" style="min-width: 120px" data-sortable="false">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-chat-deposit" tabIndex="-1">
        <div class="modal-dialog ">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        ×
                    </button>
                    <h4 class="modal-title">聊天,充值单ID</h4>
                    <audio id="mediaMessageID" style="display:none" src="/assets/sound/message.mp3" preload="auto" controls></audio>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <script src="/assets/js/app/common.js" charset="UTF-8"></script>
    <script src="/assets/js/clipboard.min.js" charset="UTF-8"></script>
    <script src="/assets/plugins/laydate/laydate.js" charset="UTF-8"></script>
    <script>
        laydate.skin('lynn');
        var layConfig = {
            elem: '#start_date',
            event: 'focus',
            format: 'YYYY-MM-DD hh:mm:ss',
            istime: true,
            istoday: true,
            zindex: 2
        };
        laydate(layConfig);
        layConfig.elem = '#end_date';
        laydate(layConfig);

        function showImg(url) {
            var $textAndPic = $('<div></div>');
            $textAndPic.append('<img style="width: 100%" src="'+url+'" />');
            BootstrapDialog.show({
                title:'图片',
                size:BootstrapDialog.SIZE_WIDE,
                message:$textAndPic
            });
        }
        $(function () {
            var get_params = function (data) {
                var param = {
                    'username': $("input[name='username']").val(),
                    'start_date': $("input[name='start_date']").val(),
                    'start_date': $("input[name='start_date']").val(),
                    'end_date': $("input[name='end_date']").val(),
                    'kefu': $("select[name='kefu']").val(),
                    'status': $("select[name='status']").val(),
                };
                return $.extend({}, data, param);
            }


            var table = $("#tags-table").DataTable({
                language: app.DataTable.language(),
                order: [[1, "asc"]],
                serverSide: true,
                pageLength: 50,
                searching: false,
                // 如果请求的 url 和显示页 url 不一致，或者请求 method 不是 post 类型，
                // 要 ajax(url, type, get_params) 必须加这两参数
                ajax: app.DataTable.ajax(null, null, get_params),
                "columns": [
                    {"visible": false},
                    {"data": "id"},
                    {"data": "deposit_id"},
                    {"data": "channel_name"},
                    {"data": "username"},
                    {"data": "amount"},
                    {"data": "status"},
                    {"data": "message"},
                    {"data": "read_status"},
                    {"data": "last_at"},
                    {"data": "kefu_no"},
                    {"data": "action"}
                ],
                columnDefs: [
                    {
                        'targets': 8,
                        'render': function (data, type, row) {

                            var status_label = 'danger';
                            if (row['read_status'] == 1) {
                                status_label = "success";
                            } else if (row['read_status'] == 0) {
                                status_label = "danger";
                            }
                            return app.getLabelHtml(
                                row['read_status']==0?'未读':'已读',
                                'label-' + status_label
                            );
                        }
                    },
                    {
                        'targets': 7,
                        'render': function (data, type, row) {

                           return row['message']+(row['img']?'[图片]':'');
                        }
                    },
                    {
                        'targets': 6,
                        'render': function (data, type, row) {

                            var status_label = 'danger';
                            if (row['status'] == 2) {
                                status_label = "success";
                            } else {
                                status_label = "warning";
                            }
                            return app.getLabelHtml(
                                row['status']==2?'已入款':'未入',
                                'label-' + status_label
                            );
                        }
                    },
                    {
                        'targets': -2,
                        'render': function (data, type, row) {
                            return row['kefu_no']==0?'全部':('专员'+row['kefu_no']+'('+row['kefu'][row['kefu_no']-1]+')');
                        }
                    },
                    {
                        'targets': -1,
                        "render": function (data, type, row) {
                            var row_edit = {{ Gate::check('chatdeposit/sendmsg') ? 1 : 0 }};
                            var str = '';

                            //编辑
                            if (row_edit) {
                                str += '<a style="margin:3px;" href="javascript:;" data-user-id="'+row.user_id+'" data-status="'+row.connect_status+'" class="X-Small btn-xs text-success chat_deposit"><i class="fa fa-edit"></i> 回复</a>';
                            }

                            return str;
                        }
                    },
                ],
            });

            table.on('preXhr.dt', function () {
                //loadShow();
            });

            table.on('draw.dt', function () {
               // loadFadeOut();
            });

            table.on('xhr.dt', function (e, settings, json, xhr) {
                var mediaElement = document.getElementById('mediaElementID');
                var play = false;
                for (var i=0;i<json.data.length;i++) {
                    if(json.data[i].read_status==0){
                        play = true;
                        break;
                    }
                }
                if (play) {
                    mediaElement.play();
                } else {
                    mediaElement.pause();
                }
            });

            $('#search_btn').click(function () {
                event.preventDefault();
                table.ajax.reload();
            });
            $.fn.dataTable.ext.errMode = function () {
                app.DataTable.showErrors(arguments[0].jqXHR.responseJSON.errors);
                //loadFadeOut();
            };

            var last_id = 0;
            var item = [];
            var _intval = null;
            var is_request = false;

            // 聊天
            $(document).on( 'click',".chat_deposit", function () {
                loadShow();
                // {id:1,user_id:'20001',deposit_id:'1001',connect_status:1,created_at:'',updated_at:''}
                last_id = 0;

                var tr = $(this).parents('tr');
                item = {
                    id: tr.children('td').eq(0).text(),
                    deposit_id : tr.children('td').eq(1).text(),
                    user_id : $(this).attr('data-user-id'),
                    connect_status : $(this).attr('data-status'),
                    created_at : tr.children('td').eq(6).text(),
                };

                $.ajax({
                    url: "/chatdeposit/newMessage",
                    dataType: "json",
                    method: "get",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        id:item.id,
                        last_id: last_id
                    }
                }).done(function (json) {
                    loadFadeOut();
                    if (json.hasOwnProperty('code') && json.code == '302') {
                        window.location.reload();
                    }
                    if (json.hasOwnProperty('status') && json.status == '0') {
                        //console.log(json.data);
                        var messages = [];
                        for(var x in json.data){
                            messages.push({
                                id:json.data[x].id,
                                from_user_id:json.data[x].from_user_id,
                                from_username:json.data[x].from_username,
                                to_user_id:json.data[x].to_user_id,
                                to_username:json.data[x].to_username,
                                message:json.data[x].message,
                                img:json.data[x].img,
                                time:json.data[x].created_at,
                                status:json.data[x].status,
                            });
                        }
                        var html = createChat(item,messages);
                        $('#modal-chat-deposit .modal-body').html(html);
                        $('#modal-chat-deposit').modal();
                        setTimeout(function(){
                            $('#modal-chat-deposit .modal-body .direct-chat-messages').scrollTop($('#modal-chat-deposit .modal-body .direct-chat-messages')[0].scrollHeight);
                        },200)
                    }
                });
                clearMessageInterval();

                if( _intval != null ){
                    clearInterval(_intval);
                }
                _intval = setInterval(function(){getNewMessage()},2000);
            });
            $('#modal-chat-deposit').on('hide.bs.modal', function () {

                if( _intval != null ){
                    clearInterval(_intval);
                }
            });
            $(document).on( 'click',".sendMessage", function () {
                var message = $('#message').val();
                if( message.length==0 ) return;
                $('.sendMessage').prop('disabled','disabled');
                $.ajax({
                    url: "/chatdeposit/sendmsg",
                    dataType: "json",
                    method: "POST",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        deposit_id:item.deposit_id,
                        message: message
                    }
                }).done(function (json) {
                    $('#message').val('');
                    getNewMessage();
                    $('.sendMessage').removeProp('disabled');
                });
            });

            function getNewMessage()
            {
                if( is_request ) return ;
                is_request = true;
                $.ajax({
                    url: "/chatdeposit/newMessage",
                    dataType: "json",
                    method: "get",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {
                        id:item.id,
                        last_id: last_id
                    }
                }).done(function (json) {
                    is_request = false;
                    if (json.hasOwnProperty('code') && json.code == '302') {
                        window.location.reload();
                    }
                    if (json.hasOwnProperty('status') && json.status == '0') {
                        //console.log(json.data);
                        if (json.data.length > 0) {
                            var messages = [];
                            for (var x in json.data) {
                                messages.push({
                                    id: json.data[x].id,
                                    from_user_id: json.data[x].from_user_id,
                                    from_username: json.data[x].from_username,
                                    to_user_id: json.data[x].to_user_id,
                                    to_username: json.data[x].to_username,
                                    message: json.data[x].message,
                                    img: json.data[x].img,
                                    time: json.data[x].created_at,
                                    status: json.data[x].status,
                                });
                            }
                            var html = addMessage(item, messages);
                            $('#modal-chat-deposit .modal-body .direct-chat-messages').append(html.html);
                            $('#modal-chat-deposit .unread').text(html.unread).attr('title', html.unread + ' New Messages');
                            $('#modal-chat-deposit .modal-body .direct-chat-messages').scrollTop($('#modal-chat-deposit .modal-body .direct-chat-messages')[0].scrollHeight);
                        }
                    }
                });
            }

            function addMessage(info,message)
            {
                var message_html = '';
                var unread_count = 0;
                for(var x in message ){
                    if( last_id >= message[x].id ) continue;
                    var left_direction = message[x]['from_user_id'] == info.user_id;
                    message_html +=
                        "<div class='direct-chat-msg "+(left_direction?"":"right")+"'>" +
                        "   <div class='direct-chat-info clearfix'>" +
                        "       <span class='direct-chat-name "+(left_direction?"":"pull-right")+"'>"+(message[x].to_user_id==0?message[x].from_username:'管理员')+"</span>" +
                        "       <span class='direct-chat-timestamp "+(left_direction?"pull-right":"")+"'>"+message[x].time+"</span>" +
                        "   </div>" +
                        "   <img class='direct-chat-img' src='/assets/dist/img/user"+(left_direction?'3':'1')+"-128x128.jpg' alt='Message User Image'><!-- /.direct-chat-img -->" +
                        "   <div class='direct-chat-text'>" + message[x].message + "<a onclick=\"showImg('{{$pic_url.'/storage/'}}"+message[x].img+"')\" href='javascript:;' ><img style='max-height:60px;display:"+(message[x].img?'block':'none')+"' src='{{$pic_url.'/storage/'}}"+message[x].img+"'></a></div>" +
                        "</div>";

                    last_id = message[x].id;

                    if( message[x].status == 0 ){
                        unread_count++;
                    }

                    if( message[x]['from_user_id'] != 0 ){
                        document.getElementById('mediaMessageID').play();
                    }
                }

                return {
                    html : message_html,
                    unread : unread_count,
                };
            }
            /**
             * 创建聊天窗口
             * @param info     消息类型 {id:1,user_id:'20001',deposit_id:'1001',connect_status:1,created_at:'',updated_at:''}
             * @param mesage   消息内容 {from_user_id:20001,from_username:'user',to_user_id:0,to_username:'admin','message':'这是一条信息','time':'2019-06-17 00:00:10',status:0}
             */
            function createChat( info , message ){
                var box_class =' box-danger direct-chat-danger ';
                var badge_class = 'bg-red';
                var send_btn_class = 'btn-danger';
                if( info.connect_status == 1 ){
                    box_class = ' box-primary direct-chat-primary ';
                    badge_class = 'bg-light-blue ';
                    send_btn_class = 'btn-primary';
                }

                var new_message = addMessage(info,message);
                var message_html = new_message.html;
                var unread_count = new_message.unread;

                var direct_chat =
                    "<div class='box direct-chat "+box_class+"' id='chat_deposit_"+info.id+"' style='margin-bottom: 0px;'>" +
                    "    <div class='box-header with-border'>" +
                    "        <h3 class='box-title'><a href='/deposit/?order_id="+info.deposit_id+"' title='充值申请' mounttabs>充值订单号:"+info.deposit_id+"</a></h3>" +
                    "        <div class='box-tools pull-right'>" +

                    "            <button type='button' class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i>" +
                    "            </button>" +
                    "            <button type='button' class='btn btn-box-tool' data-widget='remove'><i class='fa fa-times'></i></button>" +
                    "        </div>" +
                    "    </div>" +
                    //<!-- /.box-header -->
                    "    <div class='box-body'>" +
                    //        <!-- Conversations are loaded here -->" +
                "        <div class='direct-chat-messages'>"+message_html+"</div>" +
                //        <!-- /.direct-chat-pane -->" +
                "    </div>" +
                //   <!-- /.box-body -->" +
                "    <div class='box-footer'>" +
                "        <form action='#' method='post' onsubmit='return false;'>" +
                "            <div class='input-group'>" +
                "                <input type='text' name='message' id='message' placeholder='请输入消息...' class='form-control'>" +
                "                <span class='input-group-btn'>" +
                "            <button type='submit' class='btn btn-flat "+send_btn_class+" sendMessage'>发送</button>" +
                "          </span>" +
                "            </div>" +
                "        </form>" +
                "    </div>" +
                //   <!-- /.box-footer-->"
                "</div>";

                return direct_chat;
            }

            function clearMessageInterval(){
                if( _intval != null ){
                    clearInterval(_intval);
                }
            }

            /**
             * 过滤html标签
             * @param str
             * @returns {string}
             */
            function htmlSpecialChars(str)
            {
                var s = "";
                if (str.length == 0) return "";
                for   (var i=0; i<str.length; i++)
                {
                    switch (str.substr(i,1))
                    {
                        case "<": s += "&lt;"; break;
                        case ">": s += "&gt;"; break;
                        case "&": s += "&amp;"; break;
                        case " ":
                            if(str.substr(i + 1, 1) == " "){
                                s += " &nbsp;";
                                i++;
                            } else s += " ";
                            break;
                        case "\"": s += "&quot;"; break;
                        case "\n": s += "<br>"; break;
                        default: s += str.substr(i,1); break;
                    }
                }
                return s;
            }

            //30秒自动刷新
            var auto_refresh_interval='';
            $('input[name=auto_refresh]').change(function(){
                if( $(this).prop('checked') ){
                    auto_refresh();
                }else{
                    if( auto_refresh_interval == undefined || auto_refresh_interval == null ) return;
                    clearTimeout(auto_refresh_interval);
                }
            });
            auto_refresh();
            function auto_refresh(){
                if( auto_refresh_interval != undefined && auto_refresh_interval != null ) clearTimeout(auto_refresh_interval);
                table.ajax.reload();
                auto_refresh_interval = setTimeout(function () {
                    auto_refresh();
                },$("select[name='refresh_delay']").val()*1000);
            }
        });
        var listDialog = null;
        function setConfig() {
            listDialog =  BootstrapDialog.show({
                title:'收款账号管理',
                size:BootstrapDialog.SIZE_WIDE,
                message: $('<div></div>').load("/chatdeposit/payment?type=1"+'&kefu='+$("select[name='kefu']").val()),
            });
        }
        function addConfig() {
            BootstrapDialog.show({
                title:'添加收款账号',
                message: $('<div></div>').load("/chatdeposit/payment?act=add&kefu="+$("select[name='kefu']").val()),
                buttons: [{
                    icon: 'glyphicon glyphicon-send',
                    label: '保存设置',
                    cssClass: 'btn-primary',
                    autospin: true,
                    action: function(dialogRef){
                        dialogRef.enableButtons(false);
                        dialogRef.setClosable(false);
                        $.ajax({
                            url: "/chatdeposit/payment",
                            dataType: "json",
                            method: "POST",
                            data:$("#chat_deposit_config_add_from").serialize(),
                        }).done(function (json) {

                            var type='danger';
                            if (json.status == 0) {
                                type = 'success'
                                dialogRef.close();
                            }
                            BootstrapDialog.alert(json.msg);
                            dialogRef.enableButtons(true);
                        }).error(function (e) {
                            dialogRef.enableButtons(true);
                        });
                    }
                }, {
                    label: '取消',
                    action: function(dialogRef){
                        dialogRef.close();
                    }
                }]
            });
        }
        function changeType(type) {
            $(".type").hide();
            $("."+type).show();
        }
        $(document).on( 'click',".enabled", function () {
            var id = $(this).attr('data-id');
            var enabled = $(this).attr('data');
            BootstrapDialog.show({
                message: '确定'+(enabled==0?'停用':'启用')+'吗?',
                buttons: [ {
                    label: '单个',
                    cssClass: 'btn-primary',
                    action: function(dialogItself){
                        listDialog.close();
                        $.ajax({
                            url: "/chatdeposit/payment",
                            dataType: "json",
                            method: "POST",
                            data: {
                                id:id,
                                act:'enabled',
                                enabled: enabled
                            }
                        }).done(function (json) {
                            //BootstrapDialog.alert(json.msg);
                            dialogItself.close();
                            setConfig();
                        });
                    }
                }, {
                    label: '相同',
                    cssClass: 'btn-warning',
                    action: function(dialogItself){
                        listDialog.close();
                        $.ajax({
                            url: "/chatdeposit/payment",
                            dataType: "json",
                            method: "POST",
                            data: {
                                id:id,
                                act:'enabled',
                                multiple:1,
                                enabled: enabled
                            }
                        }).done(function (json) {
                            //BootstrapDialog.alert(json.msg);
                            dialogItself.close();
                            setConfig();
                        });
                    }
                },{
                    label: '取消',
                    action: function(dialogItself){
                        dialogItself.close();
                    }
                }]
            });

        });
        $(document).on( 'click',".del", function () {
            var id = $(this).attr('data-id');
            BootstrapDialog.confirm('确定要删除吗？', function(result){
                if(result) {
                    listDialog.close();
                    $.ajax({
                        url: "/chatdeposit/payment",
                        dataType: "json",
                        method: "POST",
                        data: {
                            id:id,
                            act:'del'
                        }
                    }).done(function (json) {
                        setConfig();
                        //BootstrapDialog.alert(json.msg);

                    });
                }
            });

        });
        $(document).on( 'click',".edit", function () {
            listDialog.close();
            var id = $(this).attr('data-id');
            BootstrapDialog.show({
                title:'编辑收款账号',
                message: $('<div></div>').load("/chatdeposit/payment?act=edit&id="+id),
                buttons: [{
                    icon: 'glyphicon glyphicon-send',
                    label: '保存设置',
                    cssClass: 'btn-primary',
                    autospin: true,
                    action: function(dialogRef){
                        dialogRef.enableButtons(false);
                        dialogRef.setClosable(false);
                        $.ajax({
                            url: "/chatdeposit/payment",
                            dataType: "json",
                            method: "POST",
                            data:$("#chat_deposit_config_edit_from").serialize(),
                        }).done(function (json) {

                            var type='danger';
                            if (json.status == 0) {
                                type = 'success'
                                dialogRef.close();
                            }
                            BootstrapDialog.alert(json.msg);
                            dialogRef.enableButtons(true);
                        }).error(function (e) {
                            dialogRef.enableButtons(true);
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
        function autoKeyword(){
            BootstrapDialog.show({
                title:'自动回复设置',
                message: $('<div></div>').load("/chatdeposit/autokeyword"),
                buttons: [{
                    icon: 'glyphicon glyphicon-send',
                    label: '保存设置',
                    cssClass: 'btn-primary',
                    autospin: true,
                    action: function(dialogRef){
                        dialogRef.enableButtons(false);
                        dialogRef.setClosable(false);
                        $.ajax({
                            url: "/chatdeposit/autokeyword",
                            dataType: "json",
                            method: "POST",
                            data:$("#form_auto_keyword").serialize(),
                        }).done(function (json) {

                            var type='danger';
                            if (json.status == 0) {
                                type = 'success'
                                dialogRef.close();
                            }
                            BootstrapDialog.alert(json.msg);
                            dialogRef.enableButtons(true);
                        }).error(function (e) {
                            dialogRef.enableButtons(true);
                        });
                    }
                }, {
                    label: '取消',
                    action: function(dialogRef){
                        dialogRef.close();
                    }
                }]
            });
        }
    </script>
@stop
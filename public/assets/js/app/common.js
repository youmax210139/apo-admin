/**
 * 全局的js代码.
 */

var app = {
    /**
     * 生成html片段，提供给DataTable或其他js代码使用
     * [!--可能有bug需要进一步测试--]
     *
     * ---------------------------------------------------------------------------------------------------
     * 参数格式:
     * {
     *  html:'xxx', //html标签
     *  attr:'xxx', //标签属性,格式：'键=值',例:'class="c1 c2" style="xxx" xxx="xxx"'
     *  text:'xxx'|[], //html标签内的显示内容,可以是字符串或者嵌套数组[{html:'xxx',attr:'xxx',text:'xxx'|[]}...]
     *  text_postiton : -1 | 0 | 1,// 可选参数，默认是0。此参数为标签里面内容在此标签的位置，,-1标签前面，0标签中间，1标签后面
     * }
     *
     * 使用示例：
     * apollo.renderHtml({
     *     html:'div',
     *     attr:'class="my_class_name" id="my_id" attr="attr1"',
     *     text_postiton： 0，
     *     text:'文本内容' 或者嵌套数组
     *           | [{html:'p',attr:'class="xx"',text:{嵌套}},
     *              {html:'span',attr:'class="c3"',text:'text2'}...]
     * })
     * ----------------------------------------------------------------------------------------------------
     *
     * @param obj
     * @returns str
     */
    renderHtml : function (obj){
        if(typeof obj !== 'object' &&
            (!obj.hasOwnProperty('html') ||
            !obj.hasOwnProperty('attr') ||
            !obj.hasOwnProperty('text'))) {
            alert('传参类型错误或者对象参数必须包含 html attr text 属性');
        }

        var generateHtml = {
            s_html     : '',
            s_sub_html : '',

            torender   : function(o_html) {
                var text_postiton = o_html.text_postiton || 0;
                text_postiton = (text_postiton ===-1 || text_postiton ===0 || text_postiton ===1 ) ? text_postiton : 0;

                if(typeof o_html.html !== 'string' || typeof o_html.attr !== 'string') {
                    alert('错误, html 或者 attr属性必须是字符串类型.');
                }

                if (typeof o_html.text === 'object' && !isNaN(o_html.text.length)) {
                    var sub_o_html = o_html.text;
                    for(var subobj in sub_o_html){
                        this.s_sub_html += this.torender(sub_o_html[subobj]);
                    }
                }else if(typeof o_html.text === 'string') {
                    this.s_sub_html = o_html.text;
                }

                if(text_postiton===-1){
                    this.s_html = this.s_sub_html+ '<'+o_html.html+' '+o_html.attr+'>'+'</'+o_html.html+'>';
                }

                if(text_postiton===0){
                    this.s_html = '<'+o_html.html+' '+o_html.attr+'>'+this.s_sub_html+'</'+o_html.html+'>';
                }
                if(text_postiton===1){
                    this.s_html = '<'+o_html.html+' '+o_html.attr+'>'+'</'+o_html.html+'>'+this.s_sub_html;
                }



                return this.s_html;
            }
        }

        return generateHtml.torender(obj);
    },
    /**
     *生成标签类型的html
     *
     * ---------------------------------------------------
     * 示例：app.getLabelHtml('文字','label-success');
     * ---------------------------------------------------
     *
     * @param str text //文本内容
     * @param str class_name //class名称,多个class用空格隔开
     *
     * @returns str
     */
    getLabelHtml:function(text,class_name){
        var class_name = (typeof $.trim(class_name) === 'string' && $.trim(class_name)!=='') ?
            $.trim(class_name) : '';
        var text       = (typeof $.trim(text) === 'string' && $.trim(text)!=='') ?
            $.trim(text) : '-';

        class_name = class_name? ' '+class_name : '';

        return app.renderHtml({
            html:'span',
            attr:'class="label'+class_name+'"',
            text:text
        });
    },
    /**
     * 生成带颜色的html
     *
     * ---------------------------------------------------
     * 示例：app.getColorHtml('文字','text-green',false);
     * ---------------------------------------------------
     *
     * @param str text //文本内容
     * @param str class_name //class名称,多个class用空格隔开
     * @param boolean(可选) is_strong //是否文字加粗
     *
     * @returns {*|str}
     */
    getColorHtml:function(text,class_name,is_strong){
        var class_name = (typeof $.trim(class_name) === 'string' && $.trim(class_name)!=='') ?
                          $.trim(class_name) : '';
        var text       = (typeof $.trim(text) === 'string' && $.trim(text)!=='') ?
                          $.trim(text) : '-';
        var is_strong  = typeof is_strong === 'boolean' ? is_strong : false;

        return app.renderHtml({
            html:is_strong ? 'b' : 'span',
            attr:'class="'+class_name+'"',
            text:text
        });
    },
    /**
     * 生成a标签链接html
     *
     * ---------------------------------------------------
     * 示例：app.getalinkHtml('link', {'href':'http://xxx'},'fa-edit')
     * ---------------------------------------------------
     *
     * @param str text //a标签内容
     * @param object extral_a_attr //a标签内的属性 {属性名:属性值}
     * @param string(可选)  //i标签内显示的icon class 名称
     *
     * @returns {*|str}
     */
    getalinkHtml:function(text,a_attr, icon){
        var text            = (typeof $.trim(text) === 'string' && $.trim(text)!=='') ?
                                $.trim(text) : '-';
        var a_attr          = typeof a_attr === 'object' ? a_attr : {href:'#'};
        var icon            = (typeof $.trim(icon) === 'string' && $.trim(icon)!=='') ?
                                $.trim(icon) : '';
        icon                = icon ? ' '+icon : '';

        var sub_text        = (icon=='') ?
                                text :[{
                                    html:'i',
                                    attr:'class="fa'+icon+'"',
                                    text_postiton:1,//内容text相对标签所在的位置
                                    text:text
                                }]

        var str_attr = '';
        for(var key in a_attr){
            str_attr += key+'="'+a_attr[key]+'" ';
        }
        str_attr = $.trim(str_attr)

        return app.renderHtml({
            html:'a',
            attr:str_attr,
            text:sub_text
        });
    },

    /**
     * jquery插件DataTable相关一些公用的代码统一放在这里
     * 目前后已经将language 和ajax参数提取到这里
     */
    DataTable : {
        /**
         * -------------------------------------------
         * 参数格式:
         * {k1:v1,k2:v2......}
         *
         * 使用示例：
         * apollo.DataTable.language({
         *      sProcessing:'xxx',
         *      sInfoEmpty:'xxx'
         * });
         * -------------------------------------------
         *
         * @param object
         * @returns object
         */
        language : function (o_lang) {
            var o= o_lang || {};
            var val = function(str){
                s = str || false;
                return typeof s == "string" ? s : false;
            };

            return {
                "sProcessing": val(o.sProcessing) || "处理中...",
                "sLengthMenu": val(o.sLengthMenu) || "显示 _MENU_ 项结果",
                "sZeroRecords": val(o.sZeroRecords) || "没有匹配结果",
                "sInfo": val(o.sInfo) || "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
                "sInfoEmpty": val(o.sInfoEmpty) || "显示第 0 至 0 项结果，共 0 项",

                "sInfoFiltered": val(o.sInfoFiltered) || "(由 _MAX_ 项结果过滤)",
                "sInfoPostFix": val(o.sInfoPostFix) ? o.sInfoPostFix : "",
                "sSearch":  val(o.sSearch) || "搜索:",
                "sUrl": val(o.sUrl) || "",
                "sEmptyTable": val(o.sEmptyTable) || "<p style='text-align: center'>空数据</p>",
                "sLoadingRecords": val(o.sLoadingRecords) || "载入中...",
                "sInfoThousands": val(o.sInfoThousands) || ",",
                "oPaginate": {
                    "sFirst": o.hasOwnProperty("oPaginate") ?
                              (val(o.oPaginate.sFirst) || "首页") : "首页",
                    "sPrevious": o.hasOwnProperty("oPaginate") ?
                                 (val(o.oPaginate.sPrevious) || "上页") : "上页",
                    "sNext": o.hasOwnProperty("oPaginate") ?
                             (val(o.oPaginate.sNext) || "下页") : "下页",
                    "sLast": o.hasOwnProperty("oPaginate") ?
                             (val(o.oPaginate.sLast) || "末页") : "末页",
                },
                "oAria": {
                    "sSortAscending": o.hasOwnProperty("oAria") ?
                                      (val(o.oAria.sSortAscending) || "以升序排列此列") : "以升序排列此列",
                    "sSortAscending": "以升序排列此列",
                    "sSortDescending":  o.hasOwnProperty("oAria") ?
                                        (val(o.oAria.sSortDescending) || "以降序排列此列") : "以降序排列此列",
                },
                "select" : {
                    rows: {
                        0: "",
                        _: "<span class='label-info'>%d 行已选择</span>"
                    }
                }
            };
        },
        /**
         * -------------------------------------------
         * 参数格式:
         * url:ajax地址相关|可选非必要
         * type：请求方式||可选非必要
         *
         * 使用示例：
         * apollo.DataTable.ajax(url,type);
         * -------------------------------------------
         * @param url
         * @param type
         *
         * @returns obj
         */
        ajax : function(url, type, args_cb, json_cb) {
            var url = (typeof url == "string") ? url : '';
            var type = (typeof type == "string") ? type : 'POST';

            $("input,select").popover('destroy');

            return {
                url : url,
                type: type,
                /*traditional :true,*/
                data: function (data) {
                	if (typeof args_cb == 'function') {
                		return args_cb(data);
                	} else {
                		return data;
                	}
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataSrc: function (json) {
                    if (json.hasOwnProperty('code') && json.code == '302') {
                        window.location.reload();
                    }
                    
                	if (typeof json_cb == 'function') {
                		json_cb(json);
                	}
                	
                    return json.data;
                }
            }
        },
        /**
         *错误提示
         *
         */
        showErrors: function( datatable ){
            for (var x in datatable) {
                var y= x.replace(/(\.[\d]+)$/,"");
                var obj=null;
                if ($("select[name='"+y+"']").length>=1){
                    obj = $("select[name='"+y+"']");
                }else if( $("select[name='"+y+"[]']").length>=1){
                    obj =$("select[name='"+y+"[]']");
                } else if(  $("input[name='"+y+"']").length>=1){
                    obj =$("input[name='"+y+"']");
                }else{
                    return;
                }

                obj.parents('.form-group').addClass('has-error');
                obj.attr({
                    'data-content':datatable[x].join(" "),
                    'data-trigger':'focus',
                    'data-placement':'bottom'
                }).popover('show');
            }

            $('html ,body').animate({scrollTop: 0}, 300);

            if($(".form-group").hasClass("has-error")){
                $("input,select").focus(function(){
                    $(".form-group").removeClass('has-error');
                    $("input,select").popover('destroy');
                });
            }
        }
    },
    notify:function(msg, type, timeout){
        type = (typeof $.trim(type) === 'string' && $.trim(type) !== '') ?
            $.trim(type) : 'danger';
        timeout = (typeof $.trim(timeout) === 'number' && $.trim(timeout) !== '') ?
            $.trim(timeout) : 5;
        $.notify({
            message: msg
        },{
            placement: {
                from: "top",
                align: "center"
            },
            delay: timeout,
            type: type
        });
    },

    bootoast: function (msg, type, timeout) {
        type = (typeof $.trim(type) === 'string' && $.trim(type) !== '') ?
                $.trim(type) : 'danger';
        timeout = (typeof $.trim(timeout) === 'number' && $.trim(timeout) !== '') ?
                $.trim(timeout) : 5;
        bootoast({
            message: msg,
            type: type,
            position: 'top-center',
            timeout: timeout,
            animationDuration: 300,
            dismissable: true
        });
    }
}
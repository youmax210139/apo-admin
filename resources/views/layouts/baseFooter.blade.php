    {{-- jQuery 2.2.0 --}}
    <script src="/assets/js/jquery.min.js"></script>
    {{-- Bootstrap 3.3.6 --}}
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
    {{-- AdminLTE App --}}
    <script src="/assets/dist/js/app.min.js"></script>
    {{-- dataTables --}}
    <script src="/assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/assets/plugins/datatables/dataTables.bootstrap.js"></script>
    {{-- Validator --}}
    <script src="/assets/plugins/bootstrapvalidator/bootstrapValidator.js"></script>
    <script src="/assets/plugins/jQuery-slimScroll/jquery.slimscroll.min.js"></script>
    <script src="/assets/js/jquery.cookie.js"></script>
    <script src="/assets/js/bootstrap-notify.min.js"></script>
    {{-- toast --}}
     <script src="/assets/plugins/bootoast/bootoast.js"></script>

    <script src="/assets/plugins/jQueryTimer/jquery.timers.js"></script>
    <script src="/assets/dist/js/dialog.js"></script>
    <script src="/assets/dist/js/common.js"></script>
    {{-- Optionally, you can add Slimscroll and FastClick plugins.
         Both of these plugins are recommended to enhance the
         user experience. Slimscroll is required when using the
         fixed layout. --}}
    @if(auth()->id())
       <script>
           
           var Tabs = []; //全局tabs数组
           
            $(function(){
                //皮肤切换
                $("#skins a").click(function () {
                    var skin = $.cookie('skin');
                    skin= skin?skin:'skin-blue';
                    $('body').removeClass(skin).addClass($(this).attr('data-skin'));
                    $.cookie('skin',$(this).attr('data-skin'),{ expires: 30 });

                });
                $("#iframe_refresh").click(function () { //刷新当前标签
                    $(".app-show iframe").attr('src',$(".app-show iframe").attr('src'));
                });
                $("#iframe-tabs-close-cur").click(function () { //刷新当前标签
                    var objkey    = $("#iframe-tabs li.active").index();
                    if(objkey==0){
                        return;
                    }
                    var iframe_id = $("#iframe-tabs li.active").attr('iframe_id');
                    var  curobjkey = $("#iframe-tabs li.active").index();
                   $("#iframe-tabs li.active").remove();
                    $("#app_body div").eq(objkey).remove();
                    if(curobjkey == objkey && Tabs.length>0){
                        objkey == Tabs.length ? tabsClickToFocus(objkey-1) : tabsClickToFocus(objkey+1);
                    }
                    Tabs.splice(Tabs.findIndex(val=>{return val== iframe_id }),1);//从记录数组中删除
                });
                $("#iframe-tabs-close-other").click(function () { //刷新当前标签
                    $("#iframe-tabs li").each(function () {
                        var curkey    = $("#iframe-tabs li.active").index();
                        if($(this).index()==curkey || $(this).index()==0){

                        }else{
                            var iframe_id = $(this).attr('iframe_id');
                            console.log(iframe_id)
                            console.log($(this).index()+'---')
                            $("#app_body div").eq($(this).index()).remove();
                            $(this).remove();
                            Tabs.splice(Tabs.findIndex(val=>{return val== iframe_id }),1);//从记录数组中删除
                        }
                    });


                });
                $("#iframe-tabs").on('click','button',function (event) { //关闭标签
                    event.stopPropagation();
                    var obj       = $(this).parent().parent(),
                        iframe_id = obj.attr('iframe_id');
                        objkey    = obj.index();
                        curobjkey = $("#iframe-tabs li.active").index();
                    if(curobjkey == objkey){
                        objkey == Tabs.length ? tabsClickToFocus(objkey-1) : tabsClickToFocus(objkey+1);
                    }
                    $("#app_body div").eq(objkey).remove();
                    obj.remove();
                    
                    Tabs.splice(Tabs.findIndex(val=>{return val== iframe_id }),1);//从记录数组中删除
                });
                
                $("#iframe-tabs").on('click','li',function () { //点击标签
                    $(".treeview-menu").slideUp($.AdminLTE.options.animationSpeed, function () {
                        $(this).removeClass('menu-open');
                        $(this).parent("li").removeClass("active");
                    });
                    tabsClickToFocus('',$(this));
                });
                getTasks();
                @if(Gate::check('project/alert'))
                getProjectAlert();
                @endif
                $('body').everyTime('10s',function(){
                    @if(Gate::check('project/alert'))
                    getProjectAlert();
                    @endif
                    getTasks();
                });
                setInterval('autoScroll()', 2000);

//                if( $.cookie('sidebar-toggle')=='true'){
//                    $("body").addClass("sidebar-collapse");
//                }else{
//                    $("body").removeClass("sidebar-collapse");
//                }
//                $('.sidebar-toggle').click(function(){
//                    $.cookie('sidebar-toggle', !$("body").hasClass("sidebar-collapse"),{ path: '/' });
//                });


                //----tabs滚动相关
                $("#iframe-tabs")[0].addEventListener("wheel", function(event){
                    var curLeft = $(this).scrollLeft();
                    event.deltaY > 0 ? $(this).scrollLeft(curLeft+50) : $(this).scrollLeft(curLeft-50);
                });
                $(".navTopBar .control.ltabs").click(function(){
                    var curLeft = $("#iframe-tabs").scrollLeft();
                    $("#iframe-tabs").scrollLeft(curLeft-100);
                })
                $(".navTopBar .control.rtabs").click(function(){
                    var curLeft = $("#iframe-tabs").scrollLeft();
                    $("#iframe-tabs").scrollLeft(curLeft+100);
                })
            });

           function TabsWaiter(event){
               if($(event.target).attr("mountTabs") !== undefined){ // mountTabs 是钩子属性, 例子 <a href="xxx" title="xxx" mountTabs></a>

                   event.preventDefault();

                   var linkObj = $(event.target),
                          link = linkObj.attr('href'),
                          name = linkObj.attr('title');

                   if(Tabs.includes(link)){//有，跳转焦点
                       tabsClickToFocus(Tabs.findIndex(val=>{return val==link})+1);
                       $(".app-show iframe").attr('src',$(".app-show iframe").attr('src'));//重新加载数据
                   }else{//没有，创建
                       Tabs.push(link);//推到记录数组中
                       createTabs(link,name);//创建
                       tabsClickToFocus(Tabs.length);//获取焦点
                   }

                   if(linkObj.is('.treeview-menu *')){ //最后对一些有特殊要求的元素进行处理
                       $(".treeview-menu li").removeClass('active');
                       linkObj.parent().addClass('active');
                   }
               };
           }
           
           window.addEventListener('click',TabsWaiter); //全局监听
           
           function createTabs(link,name){
               $("#iframe-tabs li").removeClass("active");
               $("#iframe-tabs").append('<li iframe_id="' + link + '" class="active">\n' +
                   '                    <a href="javascript:;">\n' + name +
                   '                        <button style="margin-left: 10px" title="关闭" type="button" class="close">×</button>\n' +
                   '                    </a></li>');
               $("#app_body div").removeClass('app-show');
               $("#app_body").append('<div iframe_id=\"' + link + '\" class="app-tabsbody-item app-show">\n' +
                   '            <iframe width="100%" height="100%" src="' + link + '" frameborder="0" class="embed-responsive-item app-iframe"></iframe>\n' +
                   '        </div>');
           }
           
           function tabsClickToFocus(_index,_obj){
               var obj     = _obj || $("#iframe-tabs li").eq(_index),
                   index   = _index || obj.index(),
                   tabLeft = $("#iframe-tabs").scrollLeft(),
                   tabsw   = $("#iframe-tabs").width(),
                   objLeft = obj.position().left - 50,
                   objw    = obj.width();
               if((objLeft + objw) > tabsw){$("#iframe-tabs").scrollLeft(tabLeft+(objLeft+objw-tabsw) + 20);}
               if(objLeft < 0){$("#iframe-tabs").scrollLeft(tabLeft+objLeft - 20);}
               $("#iframe-tabs li").removeClass('active');
               $("#app_body div").removeClass('app-show');
               obj.addClass('active');
               $("#app_body div").eq(index).addClass("app-show");
           }
           
           function getProjectAlert() {
               $.ajax({
                   type: "PUT",
                   url: "/project/alert",
                   dataType:'json',
                   headers: {
                       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                   }
               }).done(function( data ) {
                   if(data.total>0){
                       $('#project_alert_num').html(data.total).show();
                       var toast_content = '';
                       var html=' <li>\n' +
                           '                        <ul class="menu">\n';
                       $.each(data.list,function(i){
                           //console.log(data.list[i].id,data.list[i].toast,data.list[i].msg);
                           if(data.list[i].toast == 1){
                               toast_content += data.list[i].msg + '<br>';
                           }
                           html +=
                               '                            <li>\n' +
                               '                                <a>\n' +
                               '                                    <i class="fa fa-bell text-info"></i>' +data.list[i].msg+'<button title="已查看" type="button" class="close">\n' +
                               '                                ×\n' +
                               '                            </button>'+
                               '                                </a>\n' +
                               '                            </li>\n';

                       });
                       html +=  '</ul>\n' +
                           '</li>'+
                           '<li class="footer"><a mountTabs title="提示" href="/project/alert">查看</a></li>';
                       $("#project_alert_menu").html(html);
                       if(toast_content.length > 0){
                           toast_content += '<a mountTabs title="提示" href="/project/alert">点击查看</a>';
                           bootoast({
                               message: toast_content,
                               type: 'warning',
                               position: 'top-right',
                               timeout: 5,
                               animationDuration: 300,
                               dismissable: true
                           });
                       }
                   } else {
                        $('#project_alert_num').html(data.total).hide();
                        var html=' <li>\n' +
                           '                        <ul class="menu">\n';
                        html +=  '</ul>\n' +
                           '</li>'+
                           '<li class="footer"><a mountTabs title="提示" href="/project/alert">查看</a></li>';
                        $("#project_alert_menu").html(html);
                   }
               }).fail(function(){
                   $('body').stopTime();
               });
           }
           function getTasks() {
               $.ajax({
                   type: "get",
                   url: "/index/tasks",
                   dataType:'json',
                   headers: {
                       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                   }
               }).done(function( data ) {
                   if(data.length>0){
                       $('#tasks_num').html(data.length).show();
                       var html=' <li>\n' +
                           '                        <ul class="menu" style="max-height: 480px">\n';
                       $.each(data,function(i){
                           html +='<li>\n' +
                               '                                <a href="#">\n' +
                               '                                    <h3>\n' +data[i] +
                               '                                        <small class="pull-right">严重</small>\n' +
                               '                                    </h3>\n' +
                               '\n' +
                               '                                </a>\n' +
                               '                            </li>';

                       });
                       html +=  '</ul>\n' +
                           '</li>'+
                           '<li class="footer"></li>';
                       $("#tasks_alert_menu").html(html);

                       var marquee_html = '<ul>';
                       $.each(data, function (i) {
                           marquee_html += '<li>' + data[i] + '</li>';
                       });
                       marquee_html += '</ul>';
                       $("#marquee_alert_menu").html(marquee_html);
                   }
               }).fail(function(){
                   $('body').stopTime();
               });
           }

           //文字滚动效果
           function autoScroll() {
               $("#marquee_alert_menu").find("ul").animate({
                   marginTop: "-50px"
               }, 500, function () {
                   $(this).css({marginTop: "0px"}).find("li:first").appendTo(this);
               })
           }
       </script>
       @endif
    @yield('js')
</body>
</html>
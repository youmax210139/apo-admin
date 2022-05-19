$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    error: function (jqXHR, textStatus, errorThrown) {
        switch (jqXHR.status) {
            case (500):
                Alert({msg: '系统内部错误'});
                break;
            case (401):
                Alert({msg: '未登录或者登陆超时，请重新登录！',onOk: function(){
                        top.window.location.reload();
                    }});
                break;
            case (402):
                Alert({msg: '被迫下线，你的账号在别处登陆！',onOk: function(){
                        top.window.location.reload();
                    }});
                break;
            case (302):
                Alert({msg: '未登录',onOk: function(){
                        top.window.location.reload();
                    }});
                break;
            case (403):
                Alert({msg: '您没有权限执行此操作'});
                break;
            case (408):

                Alert({msg: '请求超时'});
                break;
            default:

        }
    },
    cache: false
});
loadShow = function () {
    $("#loading").show();
}
loadFadeOut = function () {
    $("#loading").fadeOut(500);
}
function checkMoney(obj, chineseid, maxnum) {
    obj.value = formatFloat(obj.value);
    if (parseFloat(obj.value) > parseFloat(maxnum)) {
        alert("输入金额超出了可用余额");
        obj.value = maxnum;
    }
    $("#" + chineseid).html(changeMoneyToChinese(obj.value));
}
//格式化浮点数形式(只能输入正浮点数，且小数点后只能跟四位,总体数值不能大于999999999999999共15位:数值999兆)
function formatFloat(num) {
    num = num.replace(/^[^\d]/g, '');
    num = num.replace(/[^\d.]/g, '');
    num = num.replace(/\.{2,}/g, '.');
    //num = num.replace(/^[0].*/g, '');
    num = num.replace(".", "$#$").replace(/\./g, "").replace("$#$", ".");
    if (num.indexOf(".") != -1) {
        var data = num.split('.');
        num = (data[0].substr(0, 15)) + '.' + (data[1].substr(0, 2));
    } else {
        num = num.substr(0, 15);
    }
    return num;
}
//自动转换数字金额为大小写中文字符,返回大小写中文字符串，最大处理到999兆
function changeMoneyToChinese(money) {
    var cnNums = new Array("零", "壹", "贰", "叁", "肆", "伍", "陆", "柒", "捌", "玖");	//汉字的数字
    var cnIntRadice = new Array("", "拾", "佰", "仟");	//基本单位
    var cnIntUnits = new Array("", "万", "亿", "兆");	//对应整数部分扩展单位
    var cnDecUnits = new Array("角", "分", "毫", "厘");	//对应小数部分单位
    var cnInteger = "整";	//整数金额时后面跟的字符
    var cnIntLast = "元";	//整型完以后的单位
    var maxNum = 999999999999999.9999;	//最大处理的数字

    var IntegerNum;		//金额整数部分
    var DecimalNum;		//金额小数部分
    var ChineseStr = "";	//输出的中文金额字符串
    var parts;		//分离金额后用的数组，预定义

    if (money == "") {
        return "";
    }

    money = parseFloat(money);
    //alert(money);
    if (money >= maxNum) {
        $.alert('超出最大处理数字');
        return "";
    }
    if (money == 0) {
        ChineseStr = cnNums[0] + cnIntLast + cnInteger;
        //document.getElementById("show").value=ChineseStr;
        return ChineseStr;
    }
    money = money.toString(); //转换为字符串
    if (money.indexOf(".") == -1) {
        IntegerNum = money;
        DecimalNum = '';
    } else {
        parts = money.split(".");
        IntegerNum = parts[0];
        DecimalNum = parts[1].substr(0, 4);
    }
    if (parseInt(IntegerNum, 10) > 0) {//获取整型部分转换
        zeroCount = 0;
        IntLen = IntegerNum.length;
        for (i = 0; i < IntLen; i++) {
            n = IntegerNum.substr(i, 1);
            p = IntLen - i - 1;
            q = p / 4;
            m = p % 4;
            if (n == "0") {
                zeroCount++;
            } else {
                if (zeroCount > 0) {
                    ChineseStr += cnNums[0];
                }
                zeroCount = 0;	//归零
                ChineseStr += cnNums[parseInt(n)] + cnIntRadice[m];
            }
            if (m == 0 && zeroCount < 4) {
                ChineseStr += cnIntUnits[q];
            }
        }
        ChineseStr += cnIntLast;
        //整型部分处理完毕
    }
    if (DecimalNum != '') {//小数部分
        decLen = DecimalNum.length;
        for (i = 0; i < decLen; i++) {
            n = DecimalNum.substr(i, 1);
            if (n != '0') {
                ChineseStr += cnNums[Number(n)] + cnDecUnits[i];
            }
        }
    }
    if (ChineseStr == '') {
        ChineseStr += cnNums[0] + cnIntLast + cnInteger;
    } else if (DecimalNum == '') {
        ChineseStr += cnInteger;
    }
    return ChineseStr;
}
function notify (msg, type, timeout){
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
        animate:{
            enter: "animated fadeInUp",
            exit: "animated fadeOutDown"
        },
        delay: timeout,
        type: type
    });
}


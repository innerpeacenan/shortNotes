
function l(message) {
    window.console.log(message);
}

/**
 * javascript 写个日期格式老费劲了
 */
function now(format) {
//        后期根据情况,看如果需要显示其他格式的字符,则设置能字符串解析和替换的方式(借助javascript对象)
//        if(format === undefined) format = "Y-m-d H:i:s";
    var todayTime = new Date();
    var month = todayTime.getMonth() + 1;
    var day = todayTime.getDate();
    var year = todayTime.getFullYear();
    var hours = todayTime.getHours();
    var minutes = todayTime.getMinutes();
    var seconds = todayTime.getSeconds();
    return year + '-' + month + "-" + day + "-" + hours + ":" + minutes + ":" + seconds;
}
/**
 * 如果不支持 isArray 方法的话，需要这里引入
 */
if (!Array.isArray) {
    Array.isArray = function (arg) {
        return Object.prototype.toString.call(arg) === '[object Array]';
    };
}

// 统一的全局状态管理
var VGLOBAL = new Vue({data: {
    picture_index: 1,
}});
Vue.directive('focus', {
	inserted: function (el) {
		el.focus()
	}
});

Vue.directive('highlightjs', {
	inserted: function (el) {
		var blocks = el.querySelectorAll('pre code');
		Array.prototype.forEach.call(blocks, hljs.highlightBlock);
	},
	update: function (el, binding, vnode, oldVnode) {
		if (vnode.data.domProps.innerHTML !== oldVnode.data.domProps.innerHTML) {
			var blocks = el.querySelectorAll('pre code');
			Array.prototype.forEach.call(blocks, hljs.highlightBlock);
		}

	}
});


function getParam(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    console.log('name', name);
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
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

// Cookies
function createCookie(name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();
    }
    else var expires = "";

    document.cookie = name + "=" + value + expires + "; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name, "", -1);
}


$(document)
    .bind("ajaxError", function (event, jqXHR, ajaxSettings, thrownError) {
        if (403 == jqXHR.status) {
            window.location.href = '/web/page/account/login.html';
        }
    })


$(function () {
    new Vue({
        el: '#loginForm',
        data: {
            name: "",
            passwd: "",
            errorMsg: {"name": "", "passwd": ""}
        },
        methods: {
            doLogin: function () {
                var that = this;
                $.ajax({
                    dataType: "json",
                    type: 'POST',
                    url: URL_Manager.login,
                    data: {
                        name: that.name,
                        passwd: that.passwd
                    },
                    success: function (result) {
                        if (result.status) {
                            var data = result.data;
                            that.name = data.name;
                            that.passwd = data.passwd;
                        } else {
                            var data = JSON.parse(result.data.errorMsg);
                            that.errorMsg.name = data.name;
                            that.errorMsg.passwd = data.passwd;
                        }
                    },
                    error: function (err) {
                        if(err.status == 302){
                            // 后台返回 302 表示登陆成功,可直接进入主页
                            window.location.href = '/'
                        }
                    }
                })
            }
        }
    })
})




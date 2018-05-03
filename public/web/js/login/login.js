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
                            // 刷新页面
                            window.location.href = data.redirect_to
                        } else {
                            var data = JSON.parse(result.data.errorMsg);
                            that.errorMsg.name = data.name;
                            that.errorMsg.passwd = data.passwd;
                            l(data);
                        }
                    }
                })
            }
        }
    })
})




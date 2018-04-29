<?php
$icon = "https://passport.baidu.com/passApi/img/input_icons_24.png";
?>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/css/bootstrap.css">
    <link rel="stylesheet" href="/css/index.css">
    <script src="/js/jquery-2.0.2.js"></script>
    <script src="/js/bootstrap.js"></script>

</head>
<body>
<style>
    #username {
        background: url(<?=$icon?>) no-repeat scroll 2px -65px;
        line-height: 2em;
        padding-left: 30px;
    }

    #username:focus {
        background: url(<?=$icon?>) no-repeat scroll 2px -105px;
    }

    #passwd {
        background: url(<?=$icon?>) no-repeat scroll 2px -144px;
        line-height: 2em;
        padding-left: 30px;
    }

    #passwd:focus {
        background: url(<?=$icon?>) no-repeat scroll 2px -185px;
    }
</style>
<div>
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h1 class="text-center">make life easier</h1>
            </div>
        </div>

        <div clas="row">
            <form class="form-horizontal col-sm-6" method="post">
                <div class="form-group">
                    <label class="col-sm-2 control-label">用户: </label>
                    <div class="col-sm-10">
                        <input class="form-control" id="username" name="name" value='<?= $_REQUEST['name']?? '' ?>'
                               autocomplete="off" autofocus>
                        <?= $_REQUEST['error']['name'] ?? '' ?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">密码: </label>
                    <div class="col-sm-10">
                        <input class="form-control" id="passwd" name="passwd" value="<?= $_REQUEST['passwd'] ?? '' ?>"
                               autocomplete="off" type="password">
                        <?= $_REQUEST['error']['passwd'] ?? '' ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <div class="checkbox">
                            <label><input type="checkbox" name='remeberMe' autocomplete="off">下次直接登录</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-default" id="login">登录</button>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <a class="pull-right" id="register">注册</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>

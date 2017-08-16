header 部分为四个tab：命令行模式, 事项列表, 登录

登录页面的实现：
点击登录按钮，弹出一个图层，包括用户名和密码，下次自动登录(如果配置了该选项，则设置用户的cookie过期时间为一周)


想一想，如果用户不登录，应该给用户展现一个怎样的界面?
如果用户没有登录，则显示一个欢迎的页面




[
    'REDIRECT_STATUS' => '200',
    'HTTP_HOST' => 'www.note.git',
    'HTTP_X_REAL_IP' => '192.168.31.187',
    'HTTP_CONNECTION' => 'close',
    'HTTP_PRAGMA' => 'no-cache',
    'HTTP_CACHE_CONTROL' => 'no-cache',
    'HTTP_UPGRADE_INSECURE_REQUESTS' => '1',
    'HTTP_USER_AGENT' => 'Mozilla/5.0 ',
    'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
    'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.8,zh-CN;q=0.6,zh;q=0.4',
    'SERVER_NAME' => 'www.note.git',
    'SERVER_ADDR' => '127.0.0.1',
    'SERVER_PORT' => '80',
    'REMOTE_ADDR' => '192.168.31.187',
    'DOCUMENT_ROOT' => '/home/wwwroot/www.note.git/public',
    'REQUEST_SCHEME' => 'http',
    'SCRIPT_FILENAME' => '/home/wwwroot/www.note.git/public/index.php',
    'REMOTE_PORT' => '44126',
    'REDIRECT_URL' => '/index/login',
    'GATEWAY_INTERFACE' => 'CGI/1.1',
    'SERVER_PROTOCOL' => 'HTTP/1.0',
    'REQUEST_METHOD' => 'GET',
    'QUERY_STRING' => '',
    'REQUEST_URI' => '/index/login',
    'SCRIPT_NAME' => '/index.php',
    'PHP_SELF' => '/index.php',
    'REQUEST_TIME' => 1502630225,
]

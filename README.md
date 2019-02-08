
一个简单的用于记录自己日常工作计划的小型单页面应用.
示例地址:
http://60.205.214.153:88/


示例账号:
name:demo
password:111111


目前主要的功能:


右侧显示各个事项的列表,拖动列表可以调整顺序. 目前实现了左侧目录增,删，改和拖拽排序一级单击checkbox完成归档的功能
右侧笔记默认支持 markdown,并支持代码高亮,点击铅笔形状的高亮按钮可隐藏对笔记内容进行编辑,并且输入框可以随着输入的高度自动调节



标准部署步骤:

### 服务器部署

cd /etc/nginx/conf.d
vim  86.conf

    server {
        listen 86;
        server_name local_86;
        root /home/wwwroot/87/public;
        index web/page/index.html index.php;
       location / {
            try_files $uri $uri/ /index.php$is_args$query_string;
        }

        location ~ \.php$ {
            try_files $uri =404;
            fastcgi_pass 127.0.0.1:9000;
            fastcgi_split_path_info ^(.+\.php)(/.+)$;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include fastcgi_params;
        }
    }


nginx -s reload

### 前端构建

apt install npm
apt install nodejs-legacy






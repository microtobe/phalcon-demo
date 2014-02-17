# 环境设置

##Nginx 配置:

```
server {
    listen      80;
    server_name phalcon-demo.fanqie88.com;
    root        /web/phalcon-demo/public;
    charset     utf-8;
    index       index.php index.html index.htm;

    try_files   $uri $uri/ @rewrite;

    location @rewrite {
        rewrite ^/(.*)$ /index.php?_url=/$1;
    }

    location ~ \.php$ {
        fastcgi_pass            127.0.0.1:9000;
        fastcgi_index           index.php;
        include                 fastcgi_params;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_param           SCRIPT_FILENAME    $document_root$fastcgi_script_name;
        fastcgi_param           PATH_INFO          $fastcgi_path_info;
        fastcgi_param           PATH_TRANSLATED    $document_root$fastcgi_path_info;
        fastcgi_param           APP_ENV            'PRODUCTION'; # PRODUCTION|TESTING|DEVELOPMENT
    }

    location ~ .*\.(gif|jpg|jpeg|png|bmp|swf|ico|js|css)$ {
        expires    30d;
        access_log off;
    }

    location ~ /\.ht {
        deny all;
    }

    access_log  logs/phalcon-demo.log  main;
}
```

# 项目安装

##自动安装 php 相关组件

安装内容包括 composer

```bash
sh bin/install.sh
```


#!/usr/bin/env bash

### 自动修正 php 编码格式 ###

bin=/usr/local/bin/php-cs-fixer

# install php-cs-fixer
if [ ! -f $bin ]; then
    echo "Preparing to install php-cs-fixer ..."
    wget http://cs.sensiolabs.org/get/php-cs-fixer.phar -O $bin
    chmod a+x $bin
    echo "php-cs-fixer has been successfully installed."
fi

dir_name=`dirname $0`
root_dir=$dir_name/..

# 需要检查的php程序目录
php_dirs=(app/functions app/config app/controllers app/library app/models app/plugins)

for dir in ${php_dirs[@]}; do
    $bin fix $root_dir/$dir --level=all --fixers=-psr0
done

echo 'done.'

#!/usr/bin/env bash

bin=/usr/local/bin/php-cs-fixer

# install php-cs-fixer
if [ ! -f $bin ]; then
    echo "Preparing to install php-cs-fixer ..."
    wget http://cs.sensiolabs.org/get/php-cs-fixer.phar -O $bin
    chmod a+x $bin
    echo "php-cs-fixer has been successfully installed."
fi

dir_name=`dirname $0`
app_dir=$dir_name/..

$bin fix $app_dir/app/common --level=all --fixers=-psr0
$bin fix $app_dir/app/config --level=all --fixers=-psr0
$bin fix $app_dir/app/controllers --level=all --fixers=-psr0
$bin fix $app_dir/app/library --level=all --fixers=-psr0
$bin fix $app_dir/app/models --level=all --fixers=-psr0
$bin fix $app_dir/app/plugins --level=all --fixers=-psr0
$bin fix $app_dir/app/tasks --level=all --fixers=-psr0

#!/usr/bin/env bash

### PHP 代码风格自动修正 ###

dir_name=`dirname $0`
app_dir=$dir_name/..

php-cs-fixer fix $app_dir/app/common --level=all --fixers=-psr0
php-cs-fixer fix $app_dir/app/config --level=all --fixers=-psr0
php-cs-fixer fix $app_dir/app/controllers --level=all --fixers=-psr0
php-cs-fixer fix $app_dir/app/library --level=all --fixers=-psr0
php-cs-fixer fix $app_dir/app/models --level=all --fixers=-psr0
php-cs-fixer fix $app_dir/app/plugin --level=all --fixers=-psr0
php-cs-fixer fix $app_dir/app/tasks --level=all --fixers=-psr0

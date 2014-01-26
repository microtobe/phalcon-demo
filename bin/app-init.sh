#!/usr/bin/env bash

### 项目自动初始化 ###

chmod +x $0
chown -R nobody.nobody $0

root_dir=$(cd "$(dirname "$0")"; cd ..; pwd)
log_dir=$root_dir/logs
cache_dir=$root_dir/app/cache
config_file=$root_dir/app/config/application.php

if [ ! -d $log_dir ]; then
    mkdir -p $log_dir
    echo "Logs directory make sure!"
fi

if [ ! -d $cache_dir ]; then
    mkdir -p $cache_dir
    echo "Cache directory make sure!"
fi

if [ ! -f $config_file ]; then
    echo "Missing configuration file: $config_file"
fi

chown -R nobody.nobody $log_dir
chown -R nobody.nobody $cache_dir

#!/usr/bin/env bash

### 项目自动初始化 ###

chmod +x $0
chown -R nobody.nobody $0

root_dir=$(cd "$(dirname "$0")"; cd ..; pwd)

# 需要创建的目录
mk_dirs=($root_dir/logs $root_dir/app/cache $root_dir/app/metadata)

# 创建目录并设定权限
for dir in ${mk_dirs[@]}; do
    if [ ! -d $dir ]; then
        mkdir -p $dir
        echo "Created Directory: $dir"
    fi
    chown -R nobody.nobody $dir
    chmod -R 770 $dir
done

config_file=$root_dir/app/config/application.php
if [ ! -f $config_file ]; then
    echo "Missing configuration file: $config_file"
fi

echo 'done.'

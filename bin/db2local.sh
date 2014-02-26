#!/usr/bin/env bash

### 数据库同步到本地 ###

all_databases=(my-dbname)
ssh='ssh root@my-remote-host'
dump='mysqldump -uroot -p**** --single-transaction'
mysql='mysql -uroot -p****'
create='CREATE DATABASE IF NOT EXISTS'

if [ ! $1 ]; then
    echo "Usage: \`$0 all\` or \`$0 table1 table2 ...\`"
    exit
elif [ $1 = 'all' ]; then
    databases=$all_databases
else
    databases=$*
fi

for dbname in ${databases[@]}; do
    $mysql -e "$create $dbname"
    echo "dumping $dbname ..."
    $ssh $dump $dbname | $mysql $dbname
done

echo "done."

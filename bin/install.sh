#!/usr/bin/env bash

dir=$(cd "$(dirname "$0")"; cd ..; pwd)
cd $dir

echo "Application Path: $dir"

# install composer
composer=/usr/bin/composer
if [ ! -f $composer ]; then
    echo "Preparing to install composer ..."
    curl -s http://getcomposer.org/installer | php
    mv composer.phar $composer
    echo "Composer has been successfully installed."
fi

# update or install composer library
lockfile=$dir/composer.lock
if [ ! -f $lockfile ]; then
    $composer install
else
    $composer update
fi

# link phalcon-devtools
phalcon=/usr/bin/phalcon
if [ ! -f $phalcon ]; then
    ln -s $dir/vendor/phalcon/devtools/phalcon.php $phalcon
    chmod ugo+x $phalcon
fi
$phalcon

#!/usr/bin/env bash
dir=$(cd "$(dirname "$0")"; cd ..; pwd)
cd $dir

# composer
composer=`which composer`
if [ "$composer" = '' ]; then
    curl -s http://getcomposer.org/installer | php
    mv composer.phar /usr/bin/composer
    echo "Composer has been successfully installed."
fi

composer install

# devtools
bin=/usr/bin/phalcon
if [ ! -f $bin ]; then
    ln -s $dir/vendor/phalcon/devtools/phalcon.php $bin
    chmod ugo+x $bin
    $bin
fi

<?php

return new Phalcon\Config(array(
    /**
     * @see http://docs.phalconphp.com/en/latest/reference/loader.html
     */
    'loader' => array(
        'dirs' => array(
            'controllersDir' => APP_PATH . '/controllers/',
            'modelsDir'      => APP_PATH . '/models/',
            'libraryDir'     => APP_PATH . '/library/',
            'pluginDir'      => APP_PATH . '/plugin/',
            // @see https://github.com/phalcon/incubator
            'incubatorDir'   => VEN_PATH . '/phalcon/incubator/Library',
        ),
        'namespaces' => array(
            'LSS' => VEN_PATH . '/openlss/lib-array2xml/LSS',
        ),
        'prefixes' => array(
        ),
    ),

    /**
     * @see http://docs.phalconphp.com/en/latest/reference/db.html
     * @see http://docs.phalconphp.com/en/latest/api/Phalcon_Db_Adapter_Pdo_Mysql.html
     */
    'database' => array(
        'adapter'  => 'Mysql',
        'host'     => 'localhost',
        'username' => 'root',
        'password' => '',
        'dbname'   => 'phalcon-demo',
        'options'  => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
            PDO::ATTR_CASE => PDO::CASE_LOWER,
        )
    ),

    /**
     * @see http://docs.phalconphp.com/en/latest/reference/url.html
     * @see http://docs.phalconphp.com/en/latest/api/Phalcon_Mvc_Url.html
     */
    'url' => array(
        'baseUri'       => '/',
        'staticBaseUri' => '/',
    ),

    /**
     * @see http://docs.phalconphp.com/en/latest/reference/views.html
     * @see http://docs.phalconphp.com/en/latest/api/Phalcon_Mvc_View.html
     */
    'view' => array(
        'viewsDir' => APP_PATH . '/views/',
    ),

    /**
     * @see http://docs.phalconphp.com/en/latest/reference/cache.html
     */
    'cache' => array(
        'frontendOptions' => array(
            'lifetime' => 86400, // 1 天
        ),
        'backendOptions' => array(
            'cacheDir' => APP_PATH . '/cache/',
        ),
    ),
));

<?php

return array(
    /**
     * @see http://docs.phalconphp.com/en/latest/reference/loader.html
     */
    'loader' => array(
        'dirs' => array(
            'controllersDir' => APP_PATH . '/controllers/',
            'modelsDir'      => APP_PATH . '/models/',
            'libraryDir'     => APP_PATH . '/library/',
            'pluginsDir'     => APP_PATH . '/plugins/',
            // @see https://github.com/phalcon/incubator
            'incubatorDir'   => VEN_PATH . '/phalcon/incubator/Library',
        ),
        'namespaces' => array(
        ),
        'prefixes' => array(
        ),
    ),

    /**
     * @see http://docs.phalconphp.com/en/latest/reference/models.html#models-meta-data
     * @see http://docs.phalconphp.com/en/latest/api/Phalcon_Mvc_Model_MetaData.html
     */
    'models' => array(
        'metadata' => array(
            'adapter' => 'files',
            'options' => array(
                'lifetime'    => 1800, // 30 minutes
                'prefix'      => '',
                'metaDataDir' => APP_PATH . '/metadata/',
            ),
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
        'password' => 'root',
        'dbname'   => 'test',
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
        'baseUri'       => HTTP_BASE,
        'staticBaseUri' => HTTP_BASE,
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

    /**
     * @see http://docs.phalconphp.com/en/latest/reference/crypt.html
     * @see http://docs.phalconphp.com/en/latest/api/Phalcon_Crypt.html
     */
    'crypt' => array(
        'salt' => 'vU!dx12^&',
    ),

    /**
     * Cookies 参数
     */
    'cookies' => array(
        'lifetime' => 604800,  // 默认生存周期 7 天，单位：秒
        'path'     => '/',     // Cookie 存储路径
        'domain'   => null,    // Cookie 域名范围
        'secure'   => false,   // 是否启用 https 连接传输
        'httponly' => false,   // 仅允许 http 访问，禁止 javascript 访问
        'encrypt'  => false,    // 是否启用 crypt 加密
    ),

    /**
     * 多语言设置
     */
    'i18n' => array(
        'name'      => 'lang', // $_REQUEST 键名 & Cookie 名
        'default'   => 'zh-cn', // 默认语言
        'directory' => APP_PATH . '/i18n/', // 语言包所在目录
        'aliases'   => array( // 语言别名
            'zh-cn' => array('zh', 'cn'),
            'zh-tw' => array('tw', 'hk', 'zh-hk'),
            'en-us' => array('en'),
        ),
        'import'    => array(), // 默认加载的语言包
    ),
);

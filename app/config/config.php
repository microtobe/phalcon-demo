<?php

/**
 * 默认时区定义
 */
date_default_timezone_set('Asia/Shanghai');

/**
 * 定义项目根目录
 */
if (! defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__DIR__)));
}

/**
 * 加载常量定义
 */
require_once ROOT_PATH . '/app/config/defined.php';

/**
 * 加载默认配置
 */
$application = new \Phalcon\Config(include ROOT_PATH . '/app/config/application.php');

/**
 * Only for phalcon webtools
 */
return new \Phalcon\Config(array(
    'database' => $application->database->toArray(),
    'application' => array(
        'controllersDir' => $application->loader->dirs->controllersDir,
        'modelsDir'      => $application->loader->dirs->modelsDir,
        'pluginsDir'     => $application->loader->dirs->pluginsDir,
        'libraryDir'     => $application->loader->dirs->libraryDir,
        'viewsDir'       => $application->view->viewsDir,
        'cacheDir'       => $application->cache->backendOptions->cacheDir,
        'baseUri'        => $application->url->baseUri,
    )
));

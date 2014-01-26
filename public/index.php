<?php

/**
 * 默认时区定义
 */
date_default_timezone_set('Asia/Shanghai');

/**
 * 设置错误报告模式
 */
error_reporting(E_ALL);

/**
 * 打开错误显示
 */
ini_set('display_errors', 'On');

/**
 * 检测框架是否安装
 */
if (! extension_loaded('phalcon')) {
    exit('Phalcon framework extension is not installed');
}

/**
 * 检测 PDO_MYSQL
 */
if (! extension_loaded('pdo_mysql')) {
    exit('PDO_MYSQL extension is not installed');
}

// 建议打开 short_open_tag
if (! ini_get('short_open_tag')) {
    exit('Please modify <php.ini> and "short_open_tag" is set to "on"');
}

/**
 * 定义项目根目录
 */
define('ROOT_PATH', dirname(__DIR__));

/**
 * Include defined constants
 */
require dirname(__DIR__) . '/app/config/defined.php';

/**
 * Include the common functions
 */
require APP_PATH . '/common/functions.php';

/**
 * Read the configuration
 */
$config = config('application');
if ($config === false) {
    trigger_error('Application configuration failed to load', E_USER_ERROR);
}

/**
 * Read auto-loader
 */
include APP_PATH . "/config/loader.php";

/**
 * Read services
 */
include APP_PATH . '/config/services.php';

/**
 * Handle the request
 */
$application = new \HMVCApplication($di);

echo $application->handle()->getContent();

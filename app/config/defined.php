<?php

/**
 * 常量定义
 */

// -----------------------------------------------------------------------------
// 路径常量定义
// -----------------------------------------------------------------------------

// WEB 所在目录
define('DOC_PATH', ROOT_PATH . '/public');

// 项目所在目录
define('APP_PATH', ROOT_PATH . '/app');

// 外部库所在目录
define('VEN_PATH', ROOT_PATH . '/vendor');

// -----------------------------------------------------------------------------
// 项目常量定义
// -----------------------------------------------------------------------------

// 定义项目开始时间
defined('START_TIME') or define('START_TIME', microtime(true));

// 定义项目初始内存
defined('START_MEMORY') or define('START_MEMORY', memory_get_usage());

// 项目版本
define('VERSION', '1.0.0');

// 生产环境
define('PRODUCTION', 'production');

// 测试环境
define('TESTING', 'testing');

// 开发环境
define('DEVELOPMENT', 'development');

/**
 * 定义开发环境
 * 如果服务器定义了 APP_ENV 变量，则以 APP_ENV 值作为环境定义
 *
 * @example for nginx config
 *     location ~ \.php$ {
 *         ...
 *         fastcgi_param LITECMS_ENV 'PRODUCTION'; # PRODUCTION|TESTING|DEVELOPMENT
 *     }
 */
if (isset($_SERVER['APP_ENV'])) {
    $env = strtoupper($_SERVER['APP_ENV']);
    if (defined($env)) {
        define('ENVIRONMENT', constant($env));
    }
    unset($env);
}

defined('ENVIRONMENT') or define('ENVIRONMENT', DEVELOPMENT);

// -----------------------------------------------------------------------------
// 环境常量定义
// -----------------------------------------------------------------------------

// 定义是否 CLI 模式
define('IS_CLI', (PHP_SAPI === 'cli'));

// 定义是否 windows 环境
define('IS_WIN', (DIRECTORY_SEPARATOR === '\\'));

// 定义是否 AJAX 请求
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) and
    'xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']));

// 定义是否 cURL 请求
define('IS_CURL', stripos($_SERVER['HTTP_USER_AGENT'], 'curl') !== false);

// 定义主机地址
if (isset($_SERVER['HTTP_HOST'])) {
    define('HTTP_HOST', strtolower($_SERVER['HTTP_HOST']));
} elseif (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
    define('HTTP_HOST', strtolower($_SERVER['HTTP_X_FORWARDED_HOST']));
}

// 定义 HTTP 协议
define('HTTP_PROTOCOL', (strpos($_SERVER['SERVER_PROTOCOL'], 'HTTPS') === FALSE) ? 'http' : 'https');

// 定义是否 SSL
define('HTTP_SSL', (strpos($_SERVER['SERVER_PROTOCOL'], 'HTTPS') !== FALSE));

// 定义当前基础域名
if ($_SERVER['SERVER_PORT'] == '80' or $_SERVER['SERVER_PORT'] == '443') {
    define('HTTP_BASE', HTTP_PROTOCOL . '://' . HTTP_HOST . '/');
} else {
    define('HTTP_BASE', HTTP_PROTOCOL . '://' . HTTP_HOST . ':' . $_SERVER['SERVER_PORT'] . '/');
}

// 定义当前页面 URL 地址
define('HTTP_URL', rtrim(HTTP_BASE, '/') . $_SERVER['REQUEST_URI']);

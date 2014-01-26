<?php

use Phalcon\DI\FactoryDefault\CLI as CliDI,
     Phalcon\CLI\Console as ConsoleApp;

// 默认时区定义
date_default_timezone_set('Asia/Shanghai');

// 设置错误报告模式
error_reporting(E_ALL | E_STRICT);

// 项目根目录
define('ROOT_PATH', dirname(__DIR__));

/**
 * Include defined constants
 */
require dirname(__DIR__) . '/app/config/defined.php';

/**
 * Include the customer functions
 */
require APP_PATH . '/common/functions.php';

 // Using the CLI factory default services container
 $di = new CliDI();

 /**
  * Register the autoloader and tell it to register the tasks directory
  */
 $loader = new \Phalcon\Loader();
 $loader->registerDirs(
     array(
         APP_PATH . '/tasks'
     )
 );
 $loader->register();

 // Load the configuration file (if any)
 if(is_readable(APP_PATH . '/config/config.php')) {
     $di->set('config', config('application'));
 }

 // Create a console application
 $console = new ConsoleApp();
 $console->setDI($di);

 /**
 * Process the console arguments
 */
 $arguments = array();
 $params = array();

 foreach($argv as $k => $arg) {
     if($k == 1) {
         $arguments['task'] = $arg;
     } elseif($k == 2) {
         $arguments['action'] = $arg;
     } elseif($k >= 3) {
        $params[] = $arg;
     }
 }

 if(count($params) > 0) {
     $arguments['params'] = $params;
 }

 // define global constants for the current task and action
 define('CURRENT_TASK', (isset($argv[1]) ? $argv[1] : null));
 define('CURRENT_ACTION', (isset($argv[2]) ? $argv[2] : null));

 try {
     // handle incoming arguments
     $console->handle($arguments);
 }
 catch (Phalcon\Exception $e) {
     echo $e->getMessage();
     exit(255);
 }

<?php
/**
 * 路由配置
 *
 * @see http://docs.phalconphp.com/en/latest/reference/routing.html
 * @see http://docs.phalconphp.com/en/latest/api/Phalcon_Mvc_Router.html
 */

$router = new Phalcon\Mvc\Router();

// 删除多余的斜线
$router->removeExtraSlashes(true);

return $router;

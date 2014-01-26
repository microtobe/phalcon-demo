<?php

use Phalcon\Mvc\Controller,
    Phalcon\Mvc\View;

/**
 * 基础控制器类
 */
class ControllerBase extends Controller
{

    /**
     * Action 完成前执行
     */
    public function beforeExecuteRoute($dispatcher)
    {
        return true; // false 时停止执行
    }

    /**
     * 初始化方法
     *
     */
    public function initialize()
    {}

    /**
     * Action 完成后执行
     */
    public function afterExecuteRoute($dispatcher)
    {}

    /**
     * 关闭视图渲染
     */
    public function viewNoRender()
    {
        $this->view->setRenderLevel(View::LEVEL_NO_RENDER);
    }

}

<?php

class ControllerAjax extends ControllerBase
{
    public $ajax = null;

    /**
     * 初始化方法
     */
    public function initialize()
    {
        parent::initialize();

        // 禁用视图渲染
        $this->viewNoRender();

        // 初始化 AJAX 响应
        $this->ajax = new \AJAX($this->response);
    }
}

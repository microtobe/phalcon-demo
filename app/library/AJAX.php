<?php

use Phalcon\Http\Response;

class AJAX
{

    /**
     * 通用错误代码定义
     */
    const SUCCEED         = 200; // 响应成功
    const AUTH_FAILED     = 401; // 无权限
    const FORBIDDEN       = 403; // 禁止访问
    const INVALID_REQUEST = 404; // 无效请求
    const INTERNAL_ERROR  = 500; // 内部错误

    /**
     * @var Phalcon\Http\Response
     */
    protected $_response = null;

    /**
     * 构造方法
     *
     * @param Phalcon\Http\Response $response
     */
    public function __construct(Response $response = null)
    {
        if ($response !== null) {
            $this->setResponse($response);
        }
    }

    /**
     * 设置 response 响应
     *
     * @param  Phalcon\Http\Response $response
     * @return AJAX
     */
    public function setResponse(Response $response)
    {
        // set http response header
        $response->setContentType('application/json', 'utf-8');

        $this->_response = $response;

        return $this;
    }

    /**
     * 设置 response 响应
     *
     * @param  Phalcon\Http\Response $response
     * @return Phalcon\Http\Response
     */
    public function getResponse()
    {
        if ($this->_response === null) {
            $this->_response = new Response();
        }

        return $this->_response;
    }

    /**
     * AJAX 响应数据 (JSON格式)
     *
     * @param  int    $code    响应代码
     * @param  string $message 响应消息
     * @param  array  $data    响应数据
     * @return string
     */
    public function response($code, $message = null, array $data = null)
    {
        $options = defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE : null;

        // 响应数据
        $data = array(
            'code'      => (int) $code,
            'message'   => $message,
            'data'      => $data,
            'timestamp' => time(),
        );

        return $this->getResponse()->setJsonContent($data, $options);
    }

    /**
     * 响应错误信息
     *
     * @param  string $message
     * @param  array  $data
     * @return string
     */
    public function error($message = null, array $data = null)
    {
        return $this->response(AJAX::INTERNAL_ERROR, $message, $data);
    }

    /**
     * 响应成功信息
     *
     * @param  string $message
     * @param  array  $data
     * @return string
     */
    public function success($message, array $data = null)
    {
        return $this->response(AJAX::SUCCEED, $message, $data);
    }
}

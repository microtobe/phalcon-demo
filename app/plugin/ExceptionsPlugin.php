<?php

use
    Phalcon\Mvc\Dispatcher,
    Phalcon\Events\Event,
    Phalcon\Mvc\Dispatcher\Exception as DispatchException;

/**
 * 异常处理
 */
class ExceptionsPlugin extends Phalcon\Mvc\User\Plugin
{

    public function beforeException(Event $event, Dispatcher $dispatcher, $exception)
    {
        $dispatcher->setParam('exception', $exception);

        // 错误信息
        $message = get_class($exception) . ': ' .  strip_path($exception->getMessage()) .
            ' (in ' . strip_path($exception->getFile()) . ' on line ' . $exception->getLine() . ')' .
            "\n" . strip_path($exception->getTraceAsString()) . "\n";

        // Write log to files
        write_log('errors', $message, Phalcon\Logger::ERROR, true);

        // cURL or CLI 输出
        if (IS_CURL || IS_CLI) {
            $this->response
                ->setContentType('text/plain', 'utf-8')
                ->setContent($message)
                ->send();

            return false;
        }

        $dispatcher->forward(array(
            'controller' => 'error',
            'action' => 'index'
        ));

        return false;
    }

}

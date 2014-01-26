<?php

use Phalcon\Db\Profiler,
    Phalcon\Logger;

/**
 * 数据库事件监听器
 *
 * @see http://docs.phalconphp.com/en/latest/reference/events.html
 * @see http://docs.phalconphp.com/en/latest/api/Phalcon_Db_Profiler.html
 */
class DbListener
{

    protected $_profiler;

    /**
     * Creates the profiler and starts the logging
     */
    public function __construct()
    {
        $this->_profiler = new Profiler();
    }

    /**
     * This is executed if the event triggered is 'beforeQuery'
     */
    public function beforeQuery($event, $connection)
    {
        $this->_profiler->startProfile($connection->getSQLStatement());
    }

    /**
     * This is executed if the event triggered is 'afterQuery'
     */
    public function afterQuery($event, $connection)
    {
        $this->_profiler->stopProfile();
    }

    public function getProfiler()
    {
        return $this->_profiler;
    }

}

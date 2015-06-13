<?php

namespace utilities\classes\exception;

use \utilities\classes\logger\LoggerManager as Logger;
use \utilities\classes\logger\LogLevel as LogLevel;
use \utilities\classes\ini\IniManager as Ini;

/**
* ExceptionManager
*/
class ExceptionManager extends \Exception
{
    public static $EMERGENCY =  LogLevel::EMERGENCY;
    public static $ALERT     =  LogLevel::ALERT;
    public static $CRITICAL  =  LogLevel::CRITICAL;
    public static $ERROR     =  LogLevel::ERROR;
    public static $WARNING   =  LogLevel::WARNING;
    public static $NOTICE    =  LogLevel::NOTICE;
    public static $INFO      =  LogLevel::INFO;
    public static $DEBUG     =  LogLevel::DEBUG;
    public static $PARAMETER =  LogLevel::PARAMETER;

    private $logger;
    
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        Ini::setIniFileName('conf.ini');
        
        $logger = new Logger(Ini::getParam('Exception', 'implementedLogger'));
        $logger->log($code, $message, parent::getTrace());
    }

    /**
     * Add a logger to the implemented logger
     *
     * @param int $loggerType The logger type
     */
    public function addLogger($loggerType)
    {
        $this->logger->addLogger($loggerType);
    }

    /**
     * Remove a logger to the implemented logger
     *
     * @param  int $loggerType The logger type
     */
    public function removeLogger($loggerType)
    {
        $this->logger->removeLogger($loggerType);
    }
}

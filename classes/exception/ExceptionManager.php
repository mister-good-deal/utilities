<?php

namespace utilities\classes\exception;

use \utilities\classes\logger\LoggerManager as Logger;
use \utilities\classes\logger\LogLevel as LogLevel;

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

        $logger = new Logger(array(Logger::FILE));
        $logger->log($code, $message, parent::getTrace());
    }
}

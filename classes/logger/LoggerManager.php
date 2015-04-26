<?php

namespace utilities\classes\logger;

use \utilities\classes\logger\ConsoleLogger as ConsoleLogger;
use \utilities\classes\logger\FileLogger as FileLogger;

/**
* LoggerManager
*/
class LoggerManager
{
    const FILE    = 0;
    const CONSOLE = 1;

    private $implementedLoggers = array();

    public function __construct($loggerTypes = array(self::FILE))
    {
        if (is_array($loggerTypes)) {
            foreach ($loggerTypes as $logger) {
                if ($logger === self::FILE) {
                    $this->implementedLoggers[] = new FileLogger();
                } elseif ($logger === self::CONSOLE) {
                    $this->implementedLoggers[] = new ConsoleLogger();
                }
            }
        }
    }

    /**
     * Logs avec un niveau arbitraire.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        foreach ($this->implementedLoggers as $logger) {
            $logger->log($level, $message, $context);
        }
    }
}

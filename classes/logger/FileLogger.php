<?php

namespace utilities\classes\logger;

use \utilities\classes\logger\LogLevel as LogLevel;
use \utilities\classes\ini\IniManager as Ini;
use \utilities\interfaces\logger\LoggerInterface as LoggerInterface;
use \utilities\abstracts\logger\AbstractLogger as AbstractLogger;

/**
* FileLogger
*/
class FileLogger extends AbstractLogger implements LoggerInterface
{
    private $filePath;

    public function __construct($filePath = null)
    {
        if ($filePath !== null && is_string($filePath)) {
            $this->filePath = $filePath;
        } else {
            $this->filePath = Ini::getParam('FileLogger', 'filePath');
        }
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        $this->writeInFile($message, $context);
    }

    private function writeInFile($message, $context)
    {
        $string = date('Y-m-d H:i:s')
            . "\t\t"
            . $message
            . PHP_EOL;

        file_put_contents($this->filePath, $string, FILE_APPEND);
    }
}

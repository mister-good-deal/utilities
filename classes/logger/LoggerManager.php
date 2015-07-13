<?php
/**
 * Factory to manage the different kind of logger to implement and shortcut methods call
 *
 * @category Factory
 * @author   Romain Laneuville <romain.laneuville@hotmail.fr>
 */

namespace utilities\classes\logger;

use \utilities\classes\logger\ConsoleLogger as ConsoleLogger;
use \utilities\classes\logger\FileLogger as FileLogger;

/**
* LoggerManager
*/
class LoggerManager
{
    /**
     * @const FILE    The file logger descriptor
     * @const CONSOLE The console logger descriptor
     * @notice        If you add a Logger in const there, add it in globalConstDefine method aswell
     */
    const FILE    = 1;
    const CONSOLE = 2;

    /**
     * @var int[] $implementedLoggers An array containing all the implemented loggers (represented by their descriptors)
     */
    private $implementedLoggers = array();

    /*=====================================
    =            Magic methods            =
    =====================================*/
    
    /**
     * Constructor which takes the logger type to implement in a parameter
     *
     * @param int[] $loggerTypes Loggers type to implement DEFAULT FILE
     */
    public function __construct($loggerTypes = array(self::FILE))
    {
        if (is_array($loggerTypes)) {
            foreach ($loggerTypes as $loggerType) {
                $this->addLogger($loggerType);
            }
        }
    }
    
    /*-----  End of Magic methods  ------*/
    
    /*======================================
    =            Public methods            =
    ======================================*/
    
    /**
     * Logs avec un niveau arbitraire.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     */
    public function log($level, $message, array $context = array())
    {
        foreach ($this->implementedLoggers as $loggerName => $logger) {
            $logger->log($level, $message, $context);
        }
    }

    /**
     * Add a logger to the implemented logger
     *
     * @param int $loggerType The logger type
     */
    public function addLogger($loggerType)
    {
        $loggerType = (int) $loggerType;

        if (!$this->hasLogger($loggerType)) {
            if ($loggerType === self::FILE) {
                $this->implementedLoggers[self::FILE] = new FileLogger();
            } elseif ($loggerType === self::CONSOLE) {
                $this->implementedLoggers[self::CONSOLE] = new ConsoleLogger();
            }
        }
    }
    
    /**
     * Remove a logger to the implemented logger
     *
     * @param int $loggerType The logger type
     */
    public function removeLogger($loggerType)
    {
        if ($this->hasLogger($loggerType)) {
            unset($this->implementedLoggers[$loggerType]);
        }
    }

    /**
     * Add the const definition in a global scope to use it in an INI file
     */
    public static function globalConstDefine()
    {
        if (!defined('FILE_LOGGER')) {
            define('FILE_LOGGER', self::FILE);
        }

        if (!defined('CONSOLE_LOGGER')) {
            define('CONSOLE_LOGGER', self::CONSOLE);
        }
    }
    
    /*-----  End of Public methods  ------*/
    
    /*=======================================
    =            Private methods            =
    =======================================*/
    
    /**
     * Check if a logger is already implemented
     *
     * @param  int  $loggerType The logger type
     * @return boolean          True if the logger is already implemented else false
     */
    private function hasLogger($loggerType)
    {
        return array_key_exists($loggerType, $this->implementedLoggers);
    }
    
    /*-----  End of Private methods  ------*/
}

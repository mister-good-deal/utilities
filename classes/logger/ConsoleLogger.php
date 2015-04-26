<?php

namespace utilities\classes\logger;

use \utilities\classes\logger\LogLevel as LogLevel;
use \utilities\interfaces\logger\LoggerInterface as LoggerInterface;

/**
* ConsoleLogger class
*/
class ConsoleLogger implements LoggerInterface
{
    public static $LEVELS = array(
        LogLevel::EMERGENCY => 'emergency',
        LogLevel::ALERT     => 'alert',
        LogLevel::CRITICAL  => 'critical',
        LogLevel::ERROR     => 'error',
        LogLevel::WARNING   => 'warning',
        LogLevel::NOTICE    => 'notice',
        LogLevel::INFO      => 'info',
        LogLevel::DEBUG     => 'debug'
    );

    private $colors;
    
    public function __construct()
    {
        $this->colors = new ConsoleColors();
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function emergency($message, array $context = array())
    {
        echo $this->colors->getColoredString(
            $message,
            'white',
            'red'
        )
        . "\n"
        . $this->formatContext($context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function alert($message, array $context = array())
    {
        echo $this->colors->getColoredString(
            $message,
            'light_gray',
            'red'
        )
        . "\n"
        . $this->formatContext($context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function critical($message, array $context = array())
    {
        echo $this->colors->getColoredString($message, 'red', 'light_gray')
        . "\n"
        . $this->formatContext($context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function error($message, array $context = array())
    {
        echo $this->colors->getColoredString($message, 'light_red', 'light_gray')
        . "\n"
        . $this->formatContext($context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function warning($message, array $context = array())
    {
        echo $this->colors->getColoredString($message, 'yellow', 'black')
        . "\n"
        . $this->formatContext($context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function notice($message, array $context = array())
    {
        echo $this->colors->getColoredString($message, 'light_gray', 'black')
        . "\n"
        . $this->formatContext($context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function info($message, array $context = array())
    {
        echo $this->colors->getColoredString($message, 'light_green', 'black')
        . "\n"
        . $this->formatContext($context);
    }

    /**
     * Informations détaillées de débogage.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function debug($message, array $context = array())
    {
        echo $this->colors->getColoredString($message, 'cyan', 'black')
        . "\n"
        . $this->formatContext($context);
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
        if (in_array($level, array_keys(self::$LEVELS))) {
            call_user_func(__CLASS__ . '::' . self::$LEVELS[$level], $message, $context);
        } else {
            $this->info($message, $context);
        }
    }

    private function formatContext($contexts)
    {
        $string = '';

        foreach ($contexts as $num => $context) {
            if (is_array($context)) {
                $string .= "\n" . $this->colors->getColoredString(
                    'Context: ' . ($num + 1),
                    'yellow',
                    'black'
                ) . "\n";

                if (isset($context['file'])) {
                    $string .= "\t"
                        .  $this->colors->getColoredString('in file:', 'purple', 'black')
                        . "\t"
                        . $this->colors->getColoredString($context['file'], 'yellow', 'black')
                        . "\n";
                }

                if (isset($context['class'])) {
                    $string .= "\t"
                        .  $this->colors->getColoredString('in class:', 'purple', 'black')
                        . "\t"
                        . $this->colors->getColoredString($context['class'], 'yellow', 'black')
                        . "\n";
                }


                if (isset($context['function'])) {
                    $string .= "\t"
                        .  $this->colors->getColoredString('in function:', 'purple', 'black')
                        . "\t"
                        . $this->colors->getColoredString($context['function'], 'yellow', 'black')
                        . "\n";
                }

                if (isset($context['line'])) {
                    $string .= "\t"
                        .  $this->colors->getColoredString('at line:', 'purple', 'black')
                        . "\t"
                        . $this->colors->getColoredString($context['line'], 'yellow', 'black')
                        . "\n";
                }

                // TODO implement args display
                // if (isset($context['args'])){
                //     $string .= "\twith args:\t" . $context['args'] . "\n";
                // }
            }
        }

        return $string;
    }
}

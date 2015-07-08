<?php

namespace utilities\classes;

use \utilities\classes\exception\ExceptionManager as Exception;
use \utilities\classes\ini\IniManager as Ini;

/**
 * Database
 *
 * Singleton pattern style to handle DB connection using PDO
 */
class DataBase
{
    private static $PDO = null;
    
    public function __construct($dsn = null, $username = null, $password = null, $options = null)
    {

    }

    /**
     * Is triggered when invoking inaccessible methods in a static context
     *
     * @note                     This is so powerfull, we can call non static methods with a static call
     * @param  string $name      Name of the method being called
     * @param  array  $arguments Enumerated array containing the parameters passed to the method called
     */
    public static function __callStatic($name, $arguments = array())
    {
        self::initialize();
        call_user_func_array(array(self::$PDO, $name), $arguments);
    }

    /**
     * One time call constructor to get ini values
     */
    private static function initialize($dsn = null, $username = null, $password = null, $options = null)
    {
        if (self::$PDO === null) {
            if ($username !== null && $password !== null) {
                if ($options !== null) {
                    self::$PDO = new \PDO($dsn, $username, $password, $options);
                } else {
                    self::$PDO = new \PDO($dsn, $username, $password);
                }
            } elseif (is_string($dsn)) {
                self::$PDO = new \PDO($dsn);
            } elseif ($dsn === null) {
                // Load default parameters using reflection trick to pass params
                $reflectionClass = new \ReflectionClass('PDO');
                self::$PDO = $reflectionClass->newInstanceArgs(Ini::getSectionParams('Database'));
            } else {
                throw new Exception('The first parameter must be a string', Exception::$PARAMETER);
            }
        }
    }
}

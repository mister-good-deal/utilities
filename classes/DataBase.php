<?php

namespace utilities\classes;

use \utilities\classes\exception\ExceptionManager as Exception;
use \utilities\classes\ini\IniManager as Ini;

/**
 * Singleton pattern style to handle DB connection using PDO
 *
 * @class   Database
 * @author  Romain Laneuville <romain.laneuville@hotmail.fr>
 * @example /utilities/examples/dataBase.php                Basic use of this singleton
 *
 * PDO methods that can be called directly with the __callStatic magic method
 *
 * @method bool          beginTransaction()                                              Initiates a transaction
 * @method bool          commit()                                                        Commits a transaction
 * @method mixed         errorCode()                                                     Fetch the SQLSTATE associated with the last operation on the database handle
 * @method array         errorInfo()                                                     Fetch extended error information associated with the last operation on the database handle
 * @method int           exec(string $statement)                                         Execute an SQL statement and return the number of affected rows
 * @method mixed         getAttribute(int $attribute)                                    Retrieve a database connection attribute
 * @method array         getAvailableDrivers()                                           Return an array of available PDO drivers
 * @method bool          inTransaction()                                                 Checks if inside a transaction
 * @method string        lastInsertId(string $name = NULL)                               Returns the ID of the last inserted row or sequence value
 * @method \PDOStatement prepare(string $statement, array $driver_options = array())     Prepares a statement for execution and returns a statement object
 * @method \PDOStatement query(string $statement)                                        Executes an SQL statement, returning a result set as a PDOStatement object
 * @method string        quote(string $string, int $parameter_type = PDO::PARAM_STR )    Quotes a string for use in a query
 * @method bool          rollBack()                                                      Rolls back a transaction
 * @method bool          setAttribute(int $attribute , mixed $value)                     Set an attribute
 */
class DataBase
{
    /**
     * The path of the ini configuration file
     */
    const INI_CONF_FILE = 'conf.ini';

    /**
     * @var \PDO $PDO A PDO object DEFAULT null
     */
    private static $PDO = null;
    
    /**
     * A never called constructor (can't declare it private because it's generate error)
     */
    public function __construct()
    {

    }

    /**
     * Is triggered when invoking inaccessible methods in a static context
     *
     * @note                        This is so powerfull, we can call non static methods with a static call
     * @param  string    $name      Name of the method being called
     * @param  array     $arguments Enumerated array containing the parameters passed to the method called
     * @throws Exception            If the method called is not a PDO method
     * @static
     */
    public static function __callStatic($name, $arguments = array())
    {
        $PDO = new \ReflectionClass('\PDO');

        self::initialize();

        if ($PDO->hasMethod($name)) {
            return call_user_func_array(array(self::$PDO, $name), $arguments);
        } else {
            throw new Exception('The method "' . $name . '" is not a PDO method', Exception::$PARAMETER);
        }
    }

    /**
     * Get all the table name of he current database
     *
     * @return string[] The table name as a string array
     * @static
     */
    public static function getAllTables()
    {
        self::initialize();

        return self::$PDO->query('SHOW TABLES;')->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Delete all the rows of a table
     *
     * @param  string $tableName The table name to clean
     * @static
     */
    public static function cleanTable($tableName)
    {
        self::$PDO->exec('TRUNCATE ' . $tableName);
    }

    /**
     * Utility method to reuse the same PDO instance at each call (work like a Singleton pattern)
     *
     * @param  string $dsn      The Data Source Name, or DSN, contains the information required to connect to the database
     * @param  string $username The user name for the DSN string. This parameter is optional for some PDO drivers
     * @param  string $password The password for the DSN string. This parameter is optional for some PDO drivers
     * @param  array  $options  A key=>value array of driver-specific connection options
     */
    private static function initialize($dsn = '', $username = '', $password = '', $options = array())
    {
        if (self::$PDO === null) {
            if ($username !== '' && $password !== '') {
                if (count($options) > 0) {
                    self::$PDO = new \PDO($dsn, $username, $password, $options);
                } else {
                    self::$PDO = new \PDO($dsn, $username, $password);
                }
            } elseif ($dsn !== '') {
                self::$PDO = new \PDO($dsn);
            } else {
                Ini::setIniFileName(self::INI_CONF_FILE);
                
                // Load default database parameters
                $params = Ini::getSectionParams('Database');

                self::$PDO = new \PDO($params['dsn'], $params['username'], $params['password'], $params['options']);
            }

            // Load default PDO parameters
            $params = Ini::getSectionParams('PDO');

            foreach ($params as $paramName => $paramValue) {
                self::$PDO->setAttribute(constant('\PDO::' . $paramName), $paramValue);
            }
        }
    }
}

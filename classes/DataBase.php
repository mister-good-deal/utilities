<?php

namespace utilities\classes;

use \utilities\classes\exception\ExceptionManager as Exception;

class DataBase extends \PDO
{
    
    public function __construct($dsn, $username = null, $password = null, $options = null)
    {
        if ($username !== null && $password !== null) {
            if ($options !== null) {
                parent::__construct($dsn, $username, $password, $options);
            } else {
                parent::__construct($dsn, $username, $password);
            }
        } elseif (is_string($dsn)) {
            parent::__construct($dsn);
        } else {
            throw new Exception('The first parameter must be a string', Exception::$PARAMETER);
        }
    }
}

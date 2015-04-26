<?php
namespace utilities\classes;

use \utilities\classes\logger\LogLevel as LogLevel;

/**
* Benchmark
*/
class Benchmark
{
    private $functionsArray = array();
    
    public function __construct($functions = null)
    {
        if ($functions === null) {
            throw new \Exception("ERROR::There is no parameter", LogLevel::PARAMETER);
        }

        if (!is_array($functions)) {
            throw new \Exception("ERROR::Parameter 1 must an array of functions", LogLevel::PARAMETER);
        }

        if (count($functions) < 2) {
            throw new \Exception("ERROR::Array must contain at least 2 functions", LogLevel::PARAMETER);
        }

        foreach ($functions as $function) {
            if (!is_callable($function)) {
                throw new \Exception("ERROR::Array values must be functions", LogLevel::PARAMETER);
            }
        }
    }
}

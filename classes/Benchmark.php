<?php
namespace utilities\classes;

use \utilities\classes\LogLevel as LogLevel;

/**
* Benchmark
*/
class Benchmark
{
    private $functionsArray = array();
    
    public function __construct($functions)
    {
        if (!is_array($functions)) {
            throw new \Exception("ERROR::Parameter 1 must an array of functions", LogLevel::CRITICAL);
        }

        if (count($functions) < 2) {
            throw new \Exception("ERROR::Array must contain at least 2 functions", LogLevel::CRITICAL);
        }

        foreach ($functions as $function) {
            if (!is_callable($function)) {
                throw new \Exception("ERROR::Array values must be functions", LogLevel::CRITICAL);
            }
        }
    }
}

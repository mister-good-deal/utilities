<?php
namespace utilities\classes;

use \utilities\classes\exception\ExceptionManager as Exception;

/**
* Benchmark
*/
class Benchmark
{
    private $functionsArray = array();
    private $t1;
    private $t2;
    
    public function __construct($functions = null, $params = null)
    {
        if ($functions === null) {
            throw new Exception("ERROR::There is no parameter", Exception::$PARAMETER);
        }

        if (!is_array($functions)) {
            throw new Exception("ERROR::Parameter 1 must an array of functions", Exception::$PARAMETER);
        }

        if (count($functions) < 2) {
            throw new Exception("ERROR::Array must contain at least 2 functions", Exception::$PARAMETER);
        }

        foreach ($functions as $function) {
            if (!is_callable($function)) {
                throw new Exception("ERROR::Array values must be functions", Exception::$PARAMETER);
            }
        }
    }

    public function runByIteration($iterations)
    {
        for ($i = 0; $i < $iterations; $i++) {
            # code...
        }
    }
}

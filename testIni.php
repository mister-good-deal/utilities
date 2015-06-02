<?php

use \utilities\classes\Benchmark as Benchmark;
use \utilities\classes\exception\ExceptionManager as Exception;

spl_autoload_register(function($className) {
    $className = ltrim($className, '\\');
    $fileName  = '';
    $namespace = '';
    
    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }

    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    require_once $fileName;
});

$func1 = function ($array) {
    $sum = 0;

    foreach ($array as $value) {
        $sum += $value;
    }

    return $sum;
};

$func2 = function ($array) {
    $sum = 0;

    return array_sum($array);
};

$functions = array($func1);

try {
    $benchmark = new Benchmark($functions);
} catch (Exception $e) {
} finally {
    exit(0);
}

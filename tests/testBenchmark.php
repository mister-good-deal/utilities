<?php
/**
 * Test script for Benchmark class
 *
 * @category Test
 * @author   Romain Laneuville <romain.laneuville@hotmail.fr>
 */

use \classes\Benchmark as Benchmark;
use \classes\exception\ExceptionManager as Exception;

include_once '../autoloader.php';

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
    $benchmark = new Benchmark(false, null, 'toto', 123, array(1, false, $func2, array('prout' => null, "pipi" => "popo", "deeper" => array(1, 2, 3))));
} catch (Exception $e) {
} finally {
    exit(0);
}

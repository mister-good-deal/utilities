<?php

use \utilities\classes\ini\IniManager as Ini;
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

try {
    Ini::setParam('FileLogger', 'filePath', 'C:\prog\utilities\log2.txt');
    Ini::setParam('FileLogger', 'test', 2);
    Ini::setParam('test', 'param', 3);
    Ini::setParam('test2', 'param', 4);
    Ini::setParam('test2', 'array', array(
        'key1' => 'val1',
        'key2' => 'val2',
        'key3' => 'val3')
    );

    var_dump(Ini::getParam('test2', 'array'));
} catch (Exception $e) {
} finally {
    exit(0);
}

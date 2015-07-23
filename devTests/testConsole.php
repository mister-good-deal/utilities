<?php
/**
 * Test script for the Console class
 *
 * @category Test
 * @author   Romain Laneuville <romain.laneuville@hotmail.fr>
 */

use \classes\console\Console as Console;

include_once '../autoloader.php';

try {
    $console = new Console();
    $console->launchConsole();
        
    // echo 'éé èè àà' . PHP_EOL;
    //     echo mb_convert_encoding('éé àà èè', 'CP850') . PHP_EOL;
} catch (Exception $e) {
} finally {
    exit(0);
}

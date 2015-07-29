<?php
/**
 * Test script for ImagesManager class
 *
 * @category Test
 * @author   Romain Laneuville <romain.laneuville@hotmail.fr>
 */

use \classes\ImagesManager as Images;
use \classes\ExceptionManager as Exception;

include_once '../autoloader.php';

try {
    $image = new Images(__DIR__ . '\test.jpeg');
    // $image->generateResizedImagesByWidth();
    $image->generateResizedImagesByWidth(Images::$WIDTHS_16_9, __DIR__ . '/test/');
} catch (Exception $e) {
} finally {
    exit(0);
}

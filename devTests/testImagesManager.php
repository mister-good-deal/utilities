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
    // $image->generateResizedImagesByWidth(Images::$WIDTHS_16_9);
    $image->setImageSavePath(__DIR__ . '/test');
    $image->copyrightImage('©Rom1', 40, 'Verdana', 'top-left');

} catch (Exception $e) {
} finally {
    exit(0);
}

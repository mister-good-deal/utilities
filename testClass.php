<?php

use \utilities\classes\entities\User as User;
use \utilities\classes\exception\ExceptionManager as Exception;

include_once 'autoloader.php';

try {
    $user = new User();

    echo $user;
    $user->id   = 15;
    $user->name = 'Romain';
    echo $user;

} catch (Exception $e) {
} finally {
    exit(0);
}

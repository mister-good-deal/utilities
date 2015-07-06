<?php

use \utilities\classes\entities\User as User;
use \utilities\classes\exception\ExceptionManager as Exception;

include_once 'autoloader.php';

try {
    $user = new User();

    $user->id   = 1;
    $user->name = 'Romain';
    $user->save();

} catch (Exception $e) {
} finally {
    exit(0);
}

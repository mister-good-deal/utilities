<?php

use \utilities\classes\entities\User as User;
use \utilities\classes\exception\ExceptionManager as Exception;

include_once 'autoloader.php';

try {
    $user = new User();

    echo $user;
    $user->id   = array('id_user' => 15, 'id_user2' => 20);
    $user->name = 'Romain';
    echo $user;
    print_r($user->id);

} catch (Exception $e) {
} finally {
    exit(0);
}

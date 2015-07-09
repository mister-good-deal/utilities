<?php

use \utilities\classes\entities\User as User;
use \utilities\classes\entitiesManager\UserEntityManager as EntityManager;
use \utilities\classes\DataBase as DB;
use \utilities\classes\exception\ExceptionManager as Exception;

include_once 'autoloader.php';

try {
    /**
     * @var EntityManager
     */
    $entityManager = new EntityManager();

    $user = new User();
    $user->id   = 1;
    $user->name = 'Romain';
    // echo $user;

    $entityManager->entity = $user;
    $entityManager->save();
    
    // DB::query('SELECT * FROM users');
} catch (Exception $e) {
} finally {
    exit(0);
}

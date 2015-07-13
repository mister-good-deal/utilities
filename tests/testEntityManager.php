<?php
/**
 * Test script for entity/manager patterns
 *
 * @category Test
 * @author   Romain Laneuville <romain.laneuville@hotmail.fr>
 */

use \utilities\classes\entities\User as User;
use \utilities\classes\entitiesManager\UserEntityManager as EntityManager;
use \utilities\classes\entitiesCollection\UserCollection as Collection;
use \utilities\classes\DataBase as DB;
use \utilities\classes\exception\ExceptionManager as Exception;

include_once 'autoloader.php';

try {
    $entityManager = new EntityManager();
    $collection    = new Collection();

    for ($i = 1; $i < 11; $i++) {
        $user       = new User();
        $user->id   = $i;
        $user->name = 'User_' . $i;
        $collection->add($user);
    }

    // echo $collection . PHP_EOL;
    
    $entityManager->setEntityCollection($collection);

    if (!$entityManager->saveCollection()) {
        echo 'Insertion failed' . PHP_EOL;
    } else {
        echo 'Insertion succeeded' . PHP_EOL;
    }
} catch (Exception $e) {
} finally {
    exit(0);
}

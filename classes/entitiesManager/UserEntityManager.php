<?php
/**
 * Entity manager for he entity User
 *
 * @category EntityManager
 * @author   Romain Laneuville <romain.laneuville@hotmail.fr>
 */

namespace utilities\classes\entitiesManager;

use utilities\abstracts\designPatterns\EntityManager as EntityManager;

/**
 * Performed database action relative to the User entity class
 *
 * @class UserEntityManager
 */
class UserEntityManager extends EntityManager
{
    /**
     * Constructor which called the parent one
     */
    public function __construct()
    {
        parent::__construct();
    }
}

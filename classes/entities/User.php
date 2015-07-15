<?php
/**
 * User entity
 *
 * @category Entity
 * @author   Romain Laneuville <romain.laneuville@hotmail.fr>
 */

namespace utilities\classes\entities;

use utilities\abstracts\designPatterns\Entity as Entity;

/**
 * User entity that extends the Entity abstact class
 *
 * @class User
 */
class User extends Entity
{
    /*=====================================
    =            Magic methods            =
    =====================================*/
    
    /**
     * Constructor that calls the parent Entity constructor
     */
    public function __construct()
    {
        parent::__construct('User');
    }
    
    /*-----  End of Magic methods  ------*/
}
<?php

namespace utilities\classes\entities;

use utilities\abstracts\designPatterns\Entity as Entity;
      
class User extends Entity
{
    public function __construct()
    {
        parent::__construct('User');
    }
}

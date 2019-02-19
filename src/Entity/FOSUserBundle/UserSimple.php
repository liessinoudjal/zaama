<?php
// src/Entity/User.php

namespace App\Entity\FOSUserBundle;

use App\Entity\FOSUserBundle\User ;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="usersimple")
 */
class UserSimple extends User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    public function __construct()
    {
        parent::__construct();
        $this->addRole(static::ROLE_DEFAULT);
    }
   
}
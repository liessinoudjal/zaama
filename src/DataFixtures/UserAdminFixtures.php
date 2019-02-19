<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\FOSUserBundle\User;
use App\Entity\Profile;
use App\Entity\Departement;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAdminFixtures extends Fixture implements FixtureGroupInterface
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder= $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $userAdmin = new User();
        $profile = (new Profile())->setDepartement(new Departement());
        $userAdmin->setProfile($profile);
        $userAdmin->addRole("ROLE_ADMIN")
            ->setUsername("liess")
            ->setPassword($this->encoder->encodePassword($userAdmin,"Bercy75012"))
            ->setEnabled(true)
            ->setDateBirth(new \DateTime("1983-03-25"))
            ->setPrenom("Liess")
            ->setSexe("H")
            ->setEmail("answer123@hotmail.com");
        $manager->persist($userAdmin);

        $manager->flush();
    }
    //php bin/console doctrine:fixtures:load  --group=userAdmin --append
    public static function getGroups() : array
    {
        return ['userAdmin'];
    }
}

<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\TypeSortie;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class TypeSortieFixtures extends Fixture implements FixtureGroupInterface
{

    const Types =[
        ["Repas"],["Boire un verre"],["Danser"],["Jouer"],["Spectacle"],["Cinéma"],["Détente"],["Beauté/Soin"],["Musique"],["Plein air"],["Découverte"],["Entraide"],["Religion"]
    ];

    public function load(ObjectManager $manager)
    {

        foreach(self::Types AS $type)
        {
            $typeSortie = new TypeSortie();
            $typeSortie->setNom($type[0]);
            $manager->persist($typeSortie);
        }
        $manager->flush();
    }
    //php bin/console doctrine:fixtures:load  --group=sortie --append
    public static function getGroups(): array
        {
             return ['sortie'];
         }

}

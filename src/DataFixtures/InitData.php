<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Departement;
use App\Entity\Region;

class InitData extends Fixture
{
  
    public function load(ObjectManager $manager)
    {
        $t_regions['Ile-de-France'] = [75,77,78,91,92,93,94,95]; 
        $t_departements['75'] = 'Paris'; 
        $t_departements['77'] = 'Seine-et-Marne'; 
        $t_departements['78'] = 'Yvelines';
        $t_departements['91'] = 'Essonne'; 
        $t_departements['92'] = 'Hauts-de-Seine'; 
        $t_departements['93'] = 'Seine-Saint-Denis'; 
        $t_departements['94'] = 'Val-de-Marne'; 
        $t_departements['95'] = 'Val-d\'Oise'; 
        foreach($t_regions as $nom =>$departements)
        {
            $region = new Region();
            $region->setNom($nom);
            foreach($departements as $code )
            {
                $departement = new Departement();
                $departement->setCodeDepartement($code)->setNom($t_departements[$code]);
                $region->addDepartement($departement);
            }
            $manager->persist($region);
        }
        
        // $manager->persist($product);

        $manager->flush();
    }
}

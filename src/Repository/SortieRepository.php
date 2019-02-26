<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Entity\FOSUserBundle\User as organisateur;
use App\Entity\TypeSortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Entity\FOSUserBundle\User;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{

   private $english_days = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
   private $french_days = array('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche');
   private $english_months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
   private $french_months = array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');


    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
  
    public function getSortiesAccueil() : array
    {

        $result= $this->createQueryBuilder('s')
            ->select('s.id,o.id AS organisateur,s.nbPersonneMax AS nbPersonneMax,o.sexe AS sexe, o.username AS username,s.dateSortie, s.intitule, tp.id AS typeSortie ,s.heureSortie, SUBSTRING(s.dateSortie, 1, 4) as year, SUBSTRING(s.dateSortie, 6, 2) as month, SUBSTRING(s.dateSortie, 9, 2) as day')
            ->join('s.organisateur', 'o')
            ->join('s.typeSortie', 'tp')
            ->where("s.statut = 'Publier' ")
            ->orderBy('s.dateSortie', 'ASC')
            ->AddOrderBy('s.heure', 'ASC')
            ->AddOrderBy('s.minute', 'ASC')
            ->getQuery()
            ->getResult()
        ;
        $formaté=[];
        foreach($result as $sortie)
        {
            $year = $sortie['dateSortie']->format('Y');
            $month = str_replace($this->english_months, $this->french_months,  $sortie['dateSortie']->format('F'));
            $dayString= str_replace($this->english_days, $this->french_days, $sortie['dateSortie']->format('l'));
            $dayNumber= $sortie['dateSortie']->format('d');
           $date= $sortie['dateSortie']->format('l d F Y');

          
    //$date= str_replace($english_months, $french_months, str_replace($english_days, $french_days, $sortie['dateSortie']->format('l d F Y') ) );

           $formaté[$year][$month][$dayString." ".$dayNumber][]=$sortie; 
        }
     //  dd($formaté);
        return $formaté;
    }
  /*
    $query = $this->createQueryBuilder('v')
     ->select('count(v) as nb, SUBSTRING(v.date, 9, 2) as day')
     ->groupBy('day')
     ->orderBy('v.date', 'ASC')
    */

  
    public function getSortiesUserDashboard(User $user): ?array
    {
        return  $result= $this->createQueryBuilder('s')
        ->select('s.id,o.id AS organisateur,s.nbPersonneMax AS nbPersonneMax,o.sexe AS sexe, o.username AS username,s.dateSortie, s.intitule, tp.id AS typeSortie ,s.heureSortie, SUBSTRING(s.dateSortie, 1, 4) as year, SUBSTRING(s.dateSortie, 6, 2) as month, SUBSTRING(s.dateSortie, 9, 2) as day')
        ->join('s.organisateur', 'o')
        ->join('s.typeSortie', 'tp')
        ->where("o.id = :idUser")
        ->setParameter('idUser', $user->getId())
        ->orderBy('s.dateSortie', 'ASC')
        ->AddOrderBy('s.heure', 'ASC')
        ->AddOrderBy('s.minute', 'ASC')
        ->getQuery()
        ->getResult()
    ;
    }
  
}

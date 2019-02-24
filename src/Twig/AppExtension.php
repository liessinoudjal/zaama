<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Entity\Sortie;
use App\Entity\FOSUserBundle\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SortieRepository;


class AppExtension extends AbstractExtension
{
    private $em;
    private $repo;
    public function __construct(EntityManagerInterface $em,SortieRepository $repo){
        $this->em= $em;
        $this->repo= $repo;
    }
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('isOrganisateur', [$this, 'isOrganisateur']),
            new TwigFunction('isUserProfile', [$this, 'isUserProfile']),
            new TwigFunction('isSortieModifiable', [$this, 'isSortieModifiable']),
        ];
    }

    public function isOrganisateur(Sortie $sortie, User $user) :bool
    {
        return $sortie->getOrganisateur()->getId() == $user->getId();
    }
  
    public function isUserProfile(User $userProfile, User $user) :bool
    {
        return $userProfile->getId() == $user->getId();
    }
    public function isSortieModifiable(int $idSortie)
    {
        $sortie = $this->repo->find($idSortie);
        $dateSortie= new \DateTime($sortie->getDateSortie()->format('Y-m-d').' '.$sortie->getHeureSortie().':00');
        $now= new \Datetime('now', new \DateTimeZone('Europe/Paris'));
       // dump($dateSortie);dd($now);
        return $dateSortie >= $now ;
        //return $sortie->isModifiable();
    }
}

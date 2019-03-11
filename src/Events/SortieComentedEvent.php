<?php
namespace App\Events;

use Symfony\Component\EventDispatcher\Event;
use App\Entity\FOSUserBundle\User;
use App\Entity\Sortie;



class SortieComentedEvent extends Event
{
    const CommentaireAdded = 'Commentaire_added';

    private $sortie;
    private $auteur;
  

    public function __construct(Sortie $sortie, User $auteur)
    {
        $this->sortie= $sortie;
        $this->auteur= $auteur;
    }



    /**
     * Get the value of sortie
     */ 
    public function getSortie()
    {
        return $this->sortie;
    }

    /**
     * Get the value of auteur
     */ 
    public function getAuteur()
    {
        return $this->auteur;
    }

    public function getOrganisateur() : User
    {
        return $this->sortie->getOrganisateur();
    }
}
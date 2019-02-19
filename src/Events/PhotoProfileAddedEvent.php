<?php
namespace App\Events;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;
use App\Entity\FOSUserBundle\User;
use App\Entity\Profile;
use Doctrine\ORM\EntityManagerInterface;


class PhotoProfileAddedEvent extends Event
{
    const NAME = 'photo_profile_added';

    private $formEditPhoto;
    private $profile;
    private $manager;
    private $user;
    private $photo;
  

    public function __construct(FormInterface $formEditPhoto, User $user, Profile $profile, EntityManagerInterface $manager, ?string $photo)
    {
        $this->formEditPhoto = $formEditPhoto;
        $this->user = $user;
        $this->profile = $profile;
        $this->manager = $manager;
        $this->photo = $photo;
    }

    public function getFormEditPhoto()
    {
        return $this->formEditPhoto;
    }

    public function getUser()
    {
        return $this->user;
    }
    public function getProfile()
    {
        return $this->profile;
    }

    public function getManager()
     {
         return $this->manager;
     }

     public function getPhoto()
     {
         return $this->photo;
     }
}
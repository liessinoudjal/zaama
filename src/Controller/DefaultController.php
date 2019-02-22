<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SortieRepository;
use App\Entity\FOSUserBundle\User;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(SortieRepository $sortieRepository)
    {
        $breadcrumb=["index"=>"Accueil"];
        return $this->render('default/index.html.twig',['sorties' => $sortieRepository->getSortiesAccueil(),"breadcrumb"=>$breadcrumb ]);
    }

    /**
     * @Route("/pulse", name="pulse")
     */
    public function pulse()
    {
        $breadcrumb=["index"=>"Accueil","pulse"=>"pulse"];
        return $this->render('default/pulse.html.twig',compact("breadcrumb"));
    }

      /**
     * @Route("/show/profile/{id}", name="default_show_profile")
     */
    public function showProfile(User $user)
    {

        $this->denyAccessUnlessGranted('ROLE_USER');

        $breadcrumb=["index"=>"Accueil",""=>"Profile ".$user->getUsername()];
        return $this->render('default/show_profile.html.twig',compact("breadcrumb","user"));
    }
}

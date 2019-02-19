<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SortieRepository;

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
}

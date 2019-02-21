<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SideBarLeftController extends AbstractController
{
    /**
     * @Route("/side/bar/left", name="side_bar_left")
     */
    public function index()
    {
        return $this->render('side_bar_left/index.html.twig');
    }
}

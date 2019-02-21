<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SideBarRightController extends AbstractController
{
    /**
     * @Route("/side/bar/right", name="side_bar_right")
     */
    public function index()
    {
        return $this->render('side_bar_right/index.html.twig');
    }
}

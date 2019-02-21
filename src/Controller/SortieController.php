<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/", name="sortie_index", methods="GET")
     */
    public function index(SortieRepository $sortieRepository): Response
    {
      
        return $this->render('sortie/index.html.twig', ['sorties' => $sortieRepository->getSortiesAccueil()]);
    }

    /**
     * @Route("/new", name="sortie_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $sortie = new Sortie();
        $sortie->setOrganisateur($this->getUser());
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //dd($sortie);
            $em = $this->getDoctrine()->getManager();
            $sortie->setHeureSortie();
            $em->persist($sortie);
            $em->flush();
            $this->addFlash('success', 'Sortie créee et publiée');
            return $this->redirectToRoute('index');
        }
        $breadcrumb = ["index" => "Accueil", "sortie_new" => "nouvelle sortie", ];
        return $this->render('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
            'breadcrumb'=> $breadcrumb
        ]);
    }

    /**
     * @Route("/{id}", name="sortie_show", methods="GET")
     */
    public function show(Sortie $sortie): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $breadcrumb = ["index" => "Accueil", "" => $sortie->getIntitule(), ];
        return $this->render('sortie/show.html.twig', ['sortie' => $sortie,"breadcrumb" => $breadcrumb]);
    }

    /**
     * @Route("/{id}/edit", name="sortie_edit", methods="GET|POST")
     */
    public function edit(Request $request, Sortie $sortie): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sortie_edit', ['id' => $sortie->getId()]);
        }

        return $this->render('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sortie_delete", methods="DELETE")
     */
    public function delete(Request $request, Sortie $sortie): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($sortie);
            $em->flush();
        }

        return $this->redirectToRoute('sortie_index');
    }
}

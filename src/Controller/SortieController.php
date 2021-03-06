<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CommentaireType;
use App\Entity\Commentaire;
use App\Events\SortieComentedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
            $this->addFlash('success', 'Sortie créee');
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
     * @Route("/{id}", name="sortie_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(Sortie $sortie): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER','Sortie','Vous devez être connecté pour acceder au détail de cette sortie');
        $commentaire= new Commentaire();
        $formCommentaire = $this->createForm(CommentaireType::class, $commentaire);

    
        $commentaires=$sortie->getCommentaires();
       // dd($commentaires);
        $breadcrumb = ["index" => "Accueil", "" => $sortie->getIntitule(), ];
        return $this->render('sortie/show.html.twig', ['sortie' => $sortie,"breadcrumb" => $breadcrumb,"formCommentaire"=>$formCommentaire->createView(),"commentaires"=>$commentaires]);
    }

    /**
     * @Route("/{id}/edit", name="sortie_edit", methods="GET|POST", requirements={"id"="\d+"})
     */
    public function edit(Request $request, Sortie $sortie): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);
       // dd($sortie);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Sortie modifiée');
            return $this->redirectToRoute('sortie_show', ['id' => $sortie->getId()]);
        }
        $breadcrumb = ["index" => "Accueil", "" => $sortie->getIntitule() ];
        return $this->render('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
            'breadcrumb' => $breadcrumb
        ]);
    }

    /**
     * @Route("/delete/{id}", name="sortie_delete", methods="DELETE")
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

    /**
     * @Route("/dashboard", name="sortie_dashboard")
     */
    public function userDashboard(SortieRepository $sortieRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user= $this->getUser();
            $breadcrumb = ["index" => "Accueil", "" => 'Mon dashboard <i class="fas fa-tachometer-alt"></i>' ];
        return $this->render('sortie/dashboard.html.twig', [
            'sorties' => $sortieRepository->getSortiesUserDashboard($user),
            'breadcrumb' => $breadcrumb,
            ]);
    }
     /**
     * @Route("/add/commenaire/{id}", name="sortie_add_commentaire", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function addCommentaire(Sortie $sortie, Request $request,EventDispatcherInterface $eventDispatcher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER','Sortie','Vous devez être connecté pour acceder au détail de cette sortie');
        $commentaire= new Commentaire();
        $formCommentaire = $this->createForm(CommentaireType::class, $commentaire);
        $formCommentaire->handleRequest($request);

        if ($formCommentaire->isSubmitted() && $formCommentaire->isValid()) {
            //dd($sortie);
            $em = $this->getDoctrine()->getManager();
            $commentaire->setDate(new \DateTime());
            $commentaire->setAuteur($this->getUser());
            $sortie->addCommentaire($commentaire);
            $em->persist($sortie);
            $em->flush();
            $event = new SortieComentedEvent($sortie,$this->getUser());
            $eventDispatcher->dispatch(SortieComentedEvent::CommentaireAdded, $event);
            $this->addFlash('success', 'Commentaire ajouté');

        }
        return $this->redirectToRoute('sortie_show', ['id' => $sortie->getId()]);
    }


}

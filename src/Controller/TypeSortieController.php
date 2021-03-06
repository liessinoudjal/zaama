<?php

namespace App\Controller;

use App\Entity\TypeSortie;
use App\Form\TypeSortieType;
use App\Repository\TypeSortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/type/sortie")
 */
class TypeSortieController extends AbstractController
{

    /**
     * @Route("/", name="type_sortie_index", methods="GET")
     */
    public function index(TypeSortieRepository $typeSortieRepository) : Response
    {
        $breadcrumb = ["index" => "Accueil", "type_sortie_index" => "types de sortie", ];
        return $this->render('type_sortie/index.html.twig', ['type_sorties' => $typeSortieRepository->findAll(), "breadcrumb" => $breadcrumb]);
    }

    /**
     * @Route("/new", name="type_sortie_new", methods="GET|POST")
     */
    public function new(Request $request) : Response
    {
        $typeSortie = new TypeSortie();
        $form = $this->createForm(TypeSortieType::class, $typeSortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($typeSortie);
            $em->flush();

            return $this->redirectToRoute('type_sortie_index');
        }

        $breadcrumb = ["index" => "Accueil", "type_sortie_index" => "types de sortie", "" => "nouveau type de sortie", ];
        return $this->render('type_sortie/new.html.twig', [
            'type_sortie' => $typeSortie,
            'form' => $form->createView(),
            "breadcrumb" => $breadcrumb
        ]);
    }

    /**
     * @Route("/{id}", name="type_sortie_show", methods="GET")
     */
    public function show(TypeSortie $typeSortie) : Response
    {
        $breadcrumb = ["index" => "Accueil", "type_sortie_index" => "types de sortie", "" => "type sortie", ];
        return $this->render('type_sortie/show.html.twig', ['type_sortie' => $typeSortie, "breadcrumb" => $breadcrumb]);
    }

    /**
     * @Route("/{id}/edit", name="type_sortie_edit", methods="GET|POST")
     */
    public function edit(Request $request, TypeSortie $typeSortie) : Response
    {
        
        $icone=$typeSortie->getIcone();
        $form = $this->createForm(TypeSortieType::class, $typeSortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /////
            $file = $form->get('icone')->getData();
            if(!empty($file))
            {
                $fileName = $typeSortie->getNom()."-".$this->generateUniqueFileName().'.'.$file->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $file->move(
                    $this->getParameter('repertoire_icones_sorties'),
                    $fileName
                );
            } catch (FileException $e) {
            }
            //on efface la photo du server si elle existe deja
            //dd($fileName);
           
            if(!empty($file) && file_exists($this->getParameter('repertoire_icones_sorties')."/".$icone))
            {
                chmod($this->getParameter('repertoire_icones_sorties')."/".$icone,0777);
                unlink($this->getParameter('repertoire_icones_sorties')."/".$icone);
            }

            $typeSortie->setIcone($fileName);
            $this->getDoctrine()->getManager()->persist($typeSortie);
            $this->addFlash(
                'success',
                'Icone sauvegardée'
            );

            }
            
            /////
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('type_sortie_edit', ['id' => $typeSortie->getId()]);
        }

        $breadcrumb = ["index" => "Accueil", "type_sortie_index" => "types de sortie", "" => "Modifier type sortie", ];
        return $this->render('type_sortie/edit.html.twig', [
            'type_sortie' => $typeSortie,
            'form' => $form->createView(),
            "breadcrumb" => $breadcrumb
        ]);
    }

    /**
     * @Route("/{id}", name="type_sortie_delete", methods="DELETE")
     */
    public function delete(Request $request, TypeSortie $typeSortie) : Response
    {
        if ($this->isCsrfTokenValid('delete' . $typeSortie->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($typeSortie);
            $em->flush();
        }

        return $this->redirectToRoute('type_sortie_index');
    }


      /**
     * @return string
     */
    public static function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}

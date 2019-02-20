<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\FOSUserBundle;

use FOS\UserBundle\Controller\ProfileController as BaseRegistrationController;
use Symfony\Component\Routing\Annotation\Route;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Form\DepartementType;
use App\Entity\Profile;
use App\Repository\DepartementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\SituationType;
use App\Form\DescriptionType;
use App\Form\PhotoType;
use App\Entity\FOSUserBundle\User;
use App\Events\PhotoProfileAddedEvent;


/**
 * Controller managing the user profile.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
class ProfileController extends BaseRegistrationController
{
    private $eventDispatcher;
    private $formFactory;
    private $userManager;

    public function __construct(EventDispatcherInterface $eventDispatcher, FactoryInterface $formFactory, UserManagerInterface $userManager)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
    }

    /**
     * Show the user.
     * @Route("/profile", name="profile",methods={"POST","GET"})
     */
    public function showAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $profile= $user->getProfile();
        $formEditDepartement = $this->createForm(DepartementType::class, $profile);
        $formEditSituation = $this->createForm(SituationType::class, $profile);
        $formEditDescription = $this->createForm(DescriptionType::class, $profile);
        $formEditPhoto = $this->createForm(PhotoType::class, $profile);
        $breadcrumb=["index"=>"Accueil","profile"=>"Profile"];
        return $this->render('FOSUserBundle/Profile/show.html.twig', array(
            'user' => $user,"breadcrumb"=>$breadcrumb,
            'formEditDepartement' => $formEditDepartement->createView(),
            'formEditSituation' => $formEditSituation->createView(),
            'formEditDescription' => $formEditDescription->createView(),
            'formEditPhoto' => $formEditPhoto->createView(),
        ));
    }

    /**
     * Edit the user.
     *
     * @param Request $request
     *@Route("/profile/edit", name="profile_edit")
     * @return Response
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new FormEvent($form, $request);
            $this->eventDispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

            $this->userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_profile_show');
                $response = new RedirectResponse($url);
            }

            $this->eventDispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }
        $breadcrumb=["index"=>"Accueil","profile"=>"Profile","profile_edit"=>"Modifier mon profil"];
        return $this->render('FOSUserBundle/Profile/edit.html.twig', array(
            'form' => $form->createView(),"breadcrumb"=>$breadcrumb
        ));
    }


    /**
     * Edit the user.
     *
     * @param Request $request
     *@Route("/profile/edit/{id}", name="edit_profile_user", options = { "expose" = true },methods={"POST"})
     * @return Response
     */
    public function editProfileUser(Request $request, Profile $profile, DepartementRepository $departementRepo, EntityManagerInterface $manager)
    {
        
        if($request->isXmlHttpRequest())
        {
             $field =  $request->request->get('field');
            $inputValue =  $request->request->get('inputValue');
            if($field==="departement")
            {

                $departement= $departementRepo->find($inputValue);
                if($departement != NULL)
                {
                     $profile->setDepartement($departement);
                $manager->persist($profile);
                $nouvelValeurInput = $departement->getNom();
                }else
                {
                    $profile->setDepartement(null);
                    $manager->persist($profile);
                    $nouvelValeurInput = "";
                }
            }
            elseif($field==="situation")
            {
                $profile->setSituation($inputValue);
                $manager->persist($profile);
                $nouvelValeurInput = $inputValue;
            }
            elseif($field==="description")
            {
                $profile->setDescription($inputValue);
                $manager->persist($profile);
                $nouvelValeurInput = $inputValue;
            }
            $manager->flush();
            return  $this->json(['nouvelValeurInput'=> $nouvelValeurInput],200);
        } 
        else
        {
            
        } 
    
       
    }

    
    /**
     * Edit the user.
     *
     * @param Request $request
     *@Route("/profile/edit/photo/{id}", name="edit_profile_photo_user", options = { "expose" = true },methods={"POST"})
     * @return Response
     */
    public function editProfilePhoto(Request $request, User $user, EntityManagerInterface $manager,EventDispatcherInterface $eventDispatcher)
    {
        $profile= $user->getProfile();
       
        $formEditPhoto = $this->createForm(PhotoType::class, $profile);
        //on recupere le nom de la photo presente dans le server maintenant.avant que le Form ne l'ecrase temporairement avec le nom du fichier temp
        $photo = $profile->getPhoto();
        $formEditPhoto->handleRequest($request);
    
            if ($formEditPhoto->isSubmitted() && $formEditPhoto->isValid()) {
                //ajoute une photo au profile
                $event = new PhotoProfileAddedEvent($formEditPhoto,$user,$profile,$manager,$photo);
                $eventDispatcher->dispatch(PhotoProfileAddedEvent::NAME, $event);
                
            }
            return  $this->redirectToRoute('profile');
    }
}
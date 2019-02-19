<?php

namespace  App\Controller\FOSUserBundle;


use FOS\UserBundle\Controller\RegistrationController as BaseRegistrationController;
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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Entity\Profile;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use App\Entity\Departement;

class RegistrationController extends BaseRegistrationController
{
    private $eventDispatcher;
    private $formFactory;
    private $userManager;
    private $tokenStorage;

    public function __construct(EventDispatcherInterface $eventDispatcher, FactoryInterface $formFactory, UserManagerInterface $userManager, TokenStorageInterface $tokenStorage)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request)
    {   
     
        /** si l'utilisateur est déjà connecté on le redirige vers la page d'accueil */
        if ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('index');
        }


        $user = $this->userManager->createUser();
        $profile=(new Profile())->setDepartement(new Departement());
        $user->setProfile($profile);

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $event = new FormEvent($form, $request);
                $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

                $this->userManager->updateUser($user);

                if (null === $response = $event->getResponse()) {
                    $url = $this->generateUrl('fos_user_registration_confirmed');
                    $response = new RedirectResponse($url);
                }

                $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
               
                /**on supprime le message par defaut de fosuser et on met notre message */
                $this->setGetFlash("success","Inscription enregistrée, en attente de validation de votre e-mail.");
                /** */

                return $response;
            }
           
            $event = new FormEvent($form, $request);
            $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_FAILURE, $event);

            if (null !== $response = $event->getResponse()) {
                return $response;
            }
        }
        $breadcrumb=["index"=>"Accueil","register"=>"Inscription"];
        return $this->render('FOSUserBundle/Registration/register.html.twig', array(
            'form' => $form->createView(),'breadcrumb'=>$breadcrumb
        ));
    }

    /**
     * Tell the user to check their email provider.
     *  @Route("/register/check-email", name="register.checkmail")
     */
    public function checkEmailAction(Request $request)
    {
        $email = $request->getSession()->get('fos_user_send_confirmation_email/email');

        if (empty($email)) {
            return new RedirectResponse($this->generateUrl('fos_user_registration_register'));
        }

        $request->getSession()->remove('fos_user_send_confirmation_email/email');
        $user = $this->userManager->findUserByEmail($email);

        if (null === $user) {
            return new RedirectResponse($this->container->get('router')->generate('fos_user_security_login'));
        }
        $breadcrumb=["index"=>"Accueil","register"=>"Inscription"];
        return $this->render('FOSUserBundle/Registration/check_email.html.twig', array(
            'user' => $user,'breadcrumb'=>$breadcrumb
        ));
    }

    /**
     * Receive the confirmation token from user email provider, login the user.
     *
     * @param Request $request
     * @param string  $token
     * @Route("/register/confirm/{token}", name="register.confirm")
     * @return Response
     */
    public function confirmAction(Request $request, $token)
    {
        $userManager = $this->userManager;

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $url = $this->generateUrl('fos_user_registration_confirmed');
            $response = new RedirectResponse($url);
        }

        $this->eventDispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
    }

    /**
     * Tell the user his account is now confirmed.
     *  @Route("/register/confirmed", name="register.confirmed")
     */
    public function confirmedAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }
        $breadcrumb=["index"=>"Accueil"];
        return $this->render('FOSUserBundle/Registration/confirmed.html.twig', array(
            'user' => $user,
            'targetUrl' => $this->getTargetUrlFromSession($request->getSession()),
            'breadcrumb'=> $breadcrumb
        ));
    }

    /**
     * @return string|null
     */
    private function getTargetUrlFromSession(SessionInterface $session)
    {
        $key = sprintf('_security.%s.target_path', $this->tokenStorage->getToken()->getProviderKey());

        if ($session->has($key)) {
            return $session->get($key);
        }

        return null;
    }
    /**efface un flash par defaut et initialise un nouveau */
    private function setGetFlash(string $type, string $message): void
    {
        $this->container->get('session')->getFlashBag()->get($type);
        $this->addFlash($type,$message);
    }
}

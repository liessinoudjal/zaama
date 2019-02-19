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

use FOS\UserBundle\Controller\ResettingController as BaseResettingController;
use Symfony\Component\Routing\Annotation\Route;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseNullableUserEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Form\Factory\FactoryInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller managing the resetting of the password.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Christophe Coevoet <stof@notk.org>
 */
class ResettingController extends BaseResettingController
{
    private $eventDispatcher;
    private $formFactory;
    private $userManager;
    private $tokenGenerator;
    private $mailer;

    /**
     * @var int
     */
    private $retryTtl;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param FactoryInterface         $formFactory
     * @param UserManagerInterface     $userManager
     * @param TokenGeneratorInterface  $tokenGenerator
     * @param MailerInterface          $mailer
     * @param int                      $retryTtl
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, FactoryInterface $formFactory, UserManagerInterface $userManager, TokenGeneratorInterface $tokenGenerator, MailerInterface $mailer, int $retryTtl=3600)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->formFactory = $formFactory;
        $this->userManager = $userManager;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
        $this->retryTtl = $retryTtl;
    }

    /**
     * Request reset user password: show form.
     *  @Route("/resetting/request", name="resetting_request")
     */
    public function requestAction()
    {
        $breadcrumb=["index"=>"Accueil","resetting_request"=>"Mot de passe oublié"];
        return $this->render('FOSUserBundle/Resetting/request.html.twig',['breadcrumb'=>$breadcrumb]);
    }

    /**
     * Request reset user password: submit form and send email.
     *
     * @param Request $request
     *  @Route("/resetting/send-email", name="resetting_send_mail")
     *
     * @return Response
     */
    public function sendEmailAction(Request $request)
    {
        $username = $request->request->get('username');

        $user = $this->userManager->findUserByUsernameOrEmail($username);

        $event = new GetResponseNullableUserEvent($user, $request);
        $this->eventDispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        if (null !== $user && !$user->isPasswordRequestNonExpired($this->retryTtl)) {
            $event = new GetResponseUserEvent($user, $request);
            $this->eventDispatcher->dispatch(FOSUserEvents::RESETTING_RESET_REQUEST, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken($this->tokenGenerator->generateToken());
            }

            $event = new GetResponseUserEvent($user, $request);
            $this->eventDispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_CONFIRM, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }

            $this->mailer->sendResettingEmailMessage($user);
            $user->setPasswordRequestedAt(new \DateTime());
            $this->userManager->updateUser($user);

            $event = new GetResponseUserEvent($user, $request);
            $this->eventDispatcher->dispatch(FOSUserEvents::RESETTING_SEND_EMAIL_COMPLETED, $event);

            if (null !== $event->getResponse()) {
                return $event->getResponse();
            }
        }

        return new RedirectResponse($this->generateUrl('resetting_check_mail', array('username' => $username)));
    }

    /**
     * Tell the user to check his email provider.
     *
     * @param Request $request
     * @Route("/resetting/check-email", name="resetting_check_mail")
     * @return Response
     */
    public function checkEmailAction(Request $request)
    {
        $username = $request->query->get('username');

        if (empty($username)) {
            // the user does not come from the sendEmail action
            return new RedirectResponse($this->generateUrl('resetting_request'));
        }
        $breadcrumb=["index"=>"Accueil",""=>"Envoi du mail de réhinitialisation du mot de passe"];
        return $this->render('FOSUserBundle/Resetting/check_email.html.twig', array(
            'tokenLifetime' => ceil($this->retryTtl / 3600),
            'breadcrumb'=>$breadcrumb
        ));
    }

    /**
     * Reset user password.
     *
     * @param Request $request
     * @param string  $token
     *  @Route("/resetting/reset/{token}", name="resetting_reset")
     * @return Response
     */
    public function resetAction(Request $request, $token)
    {
        $user = $this->userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            return new RedirectResponse($this->container->get('router')->generate('security_login'));
        }

        $event = new GetResponseUserEvent($user, $request);
        $this->eventDispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event = new FormEvent($form, $request);
            $this->eventDispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);

            $this->userManager->updateUser($user);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('profile');
                $response = new RedirectResponse($url);
            }

            $this->eventDispatcher->dispatch(
                FOSUserEvents::RESETTING_RESET_COMPLETED,
                new FilterUserResponseEvent($user, $request, $response)
            );

            return $response;
        }
        $breadcrumb=["index"=>"Accueil",""=>"Réhinitialisation mot de passe"];
        return $this->render('FOSUserBundle/Resetting/reset.html.twig', array(
            'token' => $token,
            'form' => $form->createView(),
            'breadcrumb'=>$breadcrumb
        ));
    }
}

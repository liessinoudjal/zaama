<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Psr\Container\ContainerInterface;

class MailerSubscriber implements EventSubscriberInterface
{

    private $mailer;
    private $templating;
    private $container;
    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating, ContainerInterface $container){
        $this->mailer= $mailer;//$container->get('mailer');
        $this->templating= $templating;
        $this->container= $container;
       // dd($this->mailer);
    }

    public static function getSubscribedEvents()
    {
        return [
           'Commentaire_added' => 'onCommentaireAdded',
        ];
    }

    public function onCommentaireAdded($event)
    {
        $message = (new \Swift_Message('Hello Email'))
        ->setFrom('norepeat@example.com')
        ->setTo($event->getOrganisateur()->getEmail())
        ->setBody(
            $this->container->get('twig')->render(
                'Email/commentaire_added.html.twig',
                ["sortie"=>$event->getSortie(), "auteur"=> $event->getAuteur()]
            ),'text/html'
        )
    ;
    $this->mailer->send($message);

    }

  
}

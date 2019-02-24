<?php
namespace App\Listener;


use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use App\Entity\FOSUserBundle\User;

use Symfony\Component\DependencyInjection\ContainerInterface;

class ActivityListener
{
    protected $container;
    protected $em;

    public function __construct(ContainerInterface $container)
    {
        $this->container= $container ;
    }

    /**
    * Update the user "lastActivity" on each request
    * @param FilterControllerEvent $event
    */
    public function onCoreController(FilterControllerEvent $event)
    {   
        $user= $this->getUser();
       
        $em= $this->container->get('doctrine.orm.entity_manager');
        // ici nous vérifions que la requête est une "MASTER_REQUEST" pour que les sous-requête soit ingoré (par exemple si vous faites un render() dans votre template)
         // ou  qu'un token d'autentification est bien présent avant d'essayer manipuler l'utilisateur courant.
        if (!$event->isMasterRequest() || $user == null ) {
            return;
        }
        // Nous vérifions qu'un token d'autentification est bien présent avant d'essayer manipuler l'utilisateur courant.
       

            // Nous utilisons un délais pendant lequel nous considèrerons que l'utilisateur est toujours actif et qu'il n'est pas nécessaire de refaire de mise à jour
            $delay = new \Datetime();
            //$delay->modify("-2 minutes");
            $delay->setTimestamp(strtotime('2 minutes ago'));
           // dump($user->getLastActivity() );
           // dd($delay);
            // Nous vérifions que l'utilisateur est bien du bon type pour ne pas appeler getLastActivity() sur un objet autre objet User
            if ($user->getLastActivity() < $delay) {
                $user->isActiveNow(); 
                $em->persist($user);
                $em->flush($user);
            }
    }

    protected function getUser()
    {
        if (!$this->container->has('security.token_storage')) {
            throw new \LogicException('The SecurityBundle is not registered in your application. Try running "composer require symfony/security-bundle".');
        }

        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            return;
        }

        if (!\is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return;
        }

        return $user;
    }
}
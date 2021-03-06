<?php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Symfony\Component\HttpKernel\KernelEvents;
use App\Events\PhotoProfileAddedEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UtilsSubscriber implements EventSubscriberInterface
{
    private $container;

    public function __construct(ContainerInterface $container){
        $this->container = $container;
    }
    public static function getSubscribedEvents()
    {
        // Liste des évènements, méthodes et priorités
        return [
           'photo_profile_added' => [
               ['addPhoto'],
            
           ],
           
        ];
    }

    public function addPhoto(PhotoProfileAddedEvent $event) {
        $formEditPhoto = $event->getFormEditPhoto();
        $profile = $event->getProfile();
        $manager = $event->getManager();
        $user = $event->getUser();
        $photo = $event->getPhoto();
        // $file stores the uploaded PDF file
                /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
               //dump($profile);
                $file = $formEditPhoto->get('photo')->getData();
                $fileName = $user->getUsername().'-'. $this->generateUniqueFileName().'.'.$file->guessExtension();
    
                // Move the file to the directory where brochures are stored
                try {
                    $file->move(
                        $this->container->getParameter('repertoire_photos'),
                        $fileName
                    );
                } catch (FileException $e) {
                    //dd($e->getMessage());
                }
                //on efface la photo du server si elle existe deja
               /// dd($profile);
                if(!empty($photo) && file_exists($this->container->getParameter('repertoire_photos')."/".$photo))
                {
                    unlink($this->container->getParameter('repertoire_photos')."/".$photo);
                }

                $profile->setPhoto($fileName); 
                $manager->persist($profile);
                $manager->flush();
                $this->addFlash(
                    'success',
                    'Photo sauvegardée'
                );
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

    /**
     * Adds a flash message to the current session for type.
     *
     * @throws \LogicException
     *
     * @final
     */
    protected function addFlash(string $type, string $message)
    {
        if (!$this->container->has('session')) {
            throw new \LogicException('You can not use the addFlash method if sessions are disabled. Enable them in "config/packages/framework.yaml".');
        }

        $this->container->get('session')->getFlashBag()->add($type, $message);
    }
}
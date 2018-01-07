<?php

namespace AppBundle\Form\EventListener;
use AppBundle\Service\FileManager\FileManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Created by PhpStorm.
 * User: marcinmisiorek
 * Date: 13.05.2017
 * Time: 21:42
 */
class NewsTypeEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var FileManagerInterface
     */
    private $fileManager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(FileManagerInterface $fileManager, TokenStorageInterface $tokenStorage)
    {
        $this->fileManager = $fileManager;
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::POST_SUBMIT => 'onPostSubmit'
        );
    }

    public function onPostSubmit(FormEvent $event)
    {
        /* @var $news \AppBundle\Entity\News */
        $news = $event->getData();
        $form = $event->getForm();

        if($this->tokenStorage->getToken() instanceof UsernamePasswordToken) {
            $news->setUser($this->tokenStorage->getToken()->getUser());
        }

        dump($form->get('photo')->getData());

        $news->setPhotoPath($this->fileManager->copyNewsImageFile($form->get('photo')->getData()));
    }
}
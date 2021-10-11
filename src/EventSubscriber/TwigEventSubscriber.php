<?php

namespace App\EventSubscriber;

use App\Repository\InboxRepository;
use App\Repository\ProfileRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $profileRepo;
    private $inboxRepo;

    public function __construct(Environment $twig, ProfileRepository $profileRepo, InboxRepository $inboxRepo)
    {
        $this->twig = $twig;
        $this->profileRepo = $profileRepo;
        $this->inboxRepo = $inboxRepo;
    }

    public function onControllerEvent(ControllerEvent $event)
    {
        $this->twig->addGlobal('profiles', $this->profileRepo->findAll());
        //$this->twig->addGlobal('messages', $this->inboxRepo->findAll());
    }

    public static function getSubscribedEvents()
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}
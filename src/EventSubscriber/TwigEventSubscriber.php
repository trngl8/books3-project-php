<?php

namespace App\EventSubscriber;

use App\Repository\ProfileRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $profileRepo;

    public function __construct(Environment $twig, ProfileRepository $profileRepo)
    {
        $this->twig = $twig;
        $this->profileRepo = $profileRepo;
    }

    public function onControllerEvent(ControllerEvent $event)
    {
        $this->twig->addGlobal('profiles', $this->profileRepo->findAll());
    }

    public static function getSubscribedEvents()
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}
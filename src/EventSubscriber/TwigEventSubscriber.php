<?php

namespace App\EventSubscriber;

use App\Repository\InboxRepository;
use App\Repository\ProfileRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;
use Symfony\Component\Security\Core\Security;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $profileRepo;
    private $inboxRepo;
    private $security;


    public function __construct(Environment $twig, ProfileRepository $profileRepo, InboxRepository $inboxRepo, Security $security)
    {
        $this->twig = $twig;
        $this->profileRepo = $profileRepo;
        $this->inboxRepo = $inboxRepo;
        $this->security = $security;
    }

    public function onControllerEvent(ControllerEvent $event)
    {
        $this->twig->addGlobal('profiles', $this->profileRepo->findAll());
        $user = $this->security->getUser();
        if($user) {
            $this->twig->addGlobal('global_messages', $this->inboxRepo->findBy(['address' => $user->getUserIdentifier()]));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}
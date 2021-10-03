<?php

namespace App\EventSubscriber;

use App\Repository\ProfileRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class UserLocaleSubscriber implements EventSubscriberInterface
{
    private $requestStack;

    private $repo;

    public function __construct(RequestStack $requestStack, ProfileRepository $repo)
    {
        $this->requestStack = $requestStack;
        $this->repo = $repo;
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();

        $profile = $this->repo->findOneBy(['email' => $user->getUserIdentifier()]);

        if (null !== $profile->getLocale()) {
            $this->requestStack->getSession()->set('_locale', $profile->getLocale());
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
        ];
    }
}
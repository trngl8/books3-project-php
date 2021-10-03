<?php

namespace App\Controller;

use App\Repository\AccountRepository;
use App\Repository\SubscriptionAgreementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SubscriberController extends AbstractController
{
    /**
     * @Route("/account", name="account")
     */
    public function profile(AccountRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ACCOUNT');

        $user = $this->getUser();

        $subscriptions = $repo->findBy(['createdBy' => $user->getUserIdentifier()]);

        return $this->render('account/index.html.twig', [
            'subscriptions' => $subscriptions
        ]);
    }

    /**
     * @Route("/subscribes", name="subscribes")
     */
    public function subscribe(SubscriptionAgreementRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        $subscriptions = $repo->findBy(['createdBy' => $user->getUserIdentifier()]);

        return $this->render('account/index.html.twig', [
            'subscriptions' => $subscriptions
        ]);
    }


}
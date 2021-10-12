<?php


namespace App\Controller;

use App\Entity\Inbox;
use App\Repository\InboxRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InboxController extends AbstractController
{
    /**
     * @Route("/messages", name="inbox_list")
     */
    public function messages(Request $request, InboxRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        $messages = $repo->findBy(['address' => $user->getUserIdentifier()]);

        return $this->render('inbox/index.html.twig', [
            'messages' => $messages,
        ]);
    }

    /**
     * @Route("/messages/{id}/show", name="inbox_show")
     */
    public function show(Inbox $message): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        //TODo: check owner
        return $this->render('inbox/show.html.twig', [
            'message' => $message,
        ]);
    }
}
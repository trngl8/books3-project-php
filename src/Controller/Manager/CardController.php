<?php

namespace App\Controller\Manager;

use App\Entity\Card;
use App\Form\CardType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CardController extends AbstractController
{
    private $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repository = $em->getRepository(Card::class);
    }

    /**
     * @Route("/manager/cards", name="manager_cards_list")
     */
    public function list(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $cards = $this->repository->findBy([], ['updatedAt' => 'DESC']);

        return $this->render('_manage/card/list.html.twig', [
            'cards' => $cards,
        ]);
    }

    /**
     * @Route("/manager/cards/{id}/edit", name="manager_cards_edit")
     */
    public function edit(Card $card, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $form = $this->createForm(CardType::class, $card);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash(
                'success',
                'message.profile.updated'
            );

            return $this->redirectToRoute('manager_cards_list');
        }

        return $this->render('_manage/card/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/manager/cards/{id}/show", name="manager_cards_show")
     */
    public function index(Card $card): Response
    {
        return $this->render('_manage/card/show.html.twig', [
            'card' => $card,
        ]);
    }

    /**
     * @Route("/manager/cards/{id}/remove", name="manager_cards_remove")
     */
    public function remove(Card $card, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('remove', $submittedToken)) {
            $this->getDoctrine()->getManager()->remove($card);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Card removed');

            return $this->redirectToRoute('manager_cards_list');
        }

        return $this->render('_manage/card/remove.html.twig', [
            'card' => $card,
        ]);
    }


}
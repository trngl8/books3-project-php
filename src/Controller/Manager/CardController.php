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
     * @Route("/manager/card/list", name="manager_cards_list")
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
     * @Route("/manager/card/{id}/edit", name="manager_cards_edit")
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
     * @Route("/manager/card/{id}/show", name="manager_card_show")
     */
    public function index(Card $card): Response
    {
        return $this->render('_manage/card/show.html.twig', [
            'card' => $card,
        ]);
    }


}
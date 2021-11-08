<?php

namespace App\Controller\Manager;

use App\Entity\Card;
use App\Form\CardType;
use App\Service\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class CardController extends AbstractController
{
    private $repository;

    private $uploader;

    public function __construct(EntityManagerInterface $em, Uploader $uploader)
    {
        //TODO: make $em dependency

        $this->repository = $em->getRepository(Card::class);
        $this->uploader = $uploader;
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

            $cardCover = $form->get('image')->getData();

            if ($cardCover instanceof UploadedFile) {
                $originalFilename = pathinfo($cardCover->getClientOriginalName(), PATHINFO_FILENAME);
                //$newFilename = $originalFilename . '-' . uniqid() . '.' . $cardCover->guessExtension();

                try {
                    $url = $this->uploader->upload($cardCover->getRealPath());

                } catch (FileException $e) {
                    throw new \RuntimeException($e->getMessage());
                }

                $card->setCoverFilename($url);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash(
                'success',
                'message.card.updated'
            );

            return $this->redirectToRoute('manager_cards_list');
        }

        return $this->render('_manage/card/edit.html.twig', [
            'form' => $form->createView(),
            'card' => $card
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
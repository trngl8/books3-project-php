<?php

namespace App\Controller;

use App\Entity\Card;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CardController extends AbstractController
{
    /**
     * @Route("/{id}", name="card_show")
     */
    public function index(Card $card): Response
    {
        return $this->render('card/show.html.twig', [
            'card' => $card,
            'controller_name' => 'CardController',
        ]);
    }

}
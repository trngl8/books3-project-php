<?php

namespace App\Controller;

use App\Repository\CardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(CardRepository $repository): Response
    {
        $cards = $repository->findAll();

        return $this->render('default/index.html.twig', [
            'cards' => $cards,
            'controller_name' => 'DefaultController',
        ]);
    }
}
<?php

namespace App\Controller;

use App\Repository\CardRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/search", name="search")
     */
    public function search(CardRepository $repository, Request $request) : Response
    {
        $isbn = $request->get('isbn');

        $cards = $repository->findBy(['isbn' => $isbn]);

        return $this->render('default/index.html.twig', [
            'cards' => $cards,
            'controller_name' => 'DefaultController',
        ]);

    }

    /**
     * @Route("/about", name="about")
     */
    public function about(): Response
    {
        return $this->render('default/about.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}
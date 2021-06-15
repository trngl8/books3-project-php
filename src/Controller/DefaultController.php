<?php

namespace App\Controller;

use App\Entity\Card;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    private $repository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repository = $em->getRepository(Card::class);
    }

    /**
     * @Route("/", name="homepage")
     */
    public function index(Request $request): Response
    {
        $page = $request->get('page', 1);
        $max = $request->get('max', 20);
        $first = ($page - 1) * $max;

        $query = $this->repository->createQueryWithPaginator($first, $max);

        $paginator = new Paginator($query, true);

        $c = count($paginator);

        $pages = $c < $max ? [1] : range(1, intdiv($c, $max) + 1);

        return $this->render('default/index.html.twig', [
            'count' => $c,
            'pages' => $pages,
            'cards' => $paginator,
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request) : Response
    {
        $isbn = $request->get('isbn');

        $cards = $this->repository->findBy(['isbn' => $isbn]);

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

    /**
     * @Route("/donation", name="donation")
     */
    public function donation(): Response
    {
        return $this->render('default/donation.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(): Response
    {
        return $this->render('default/contact.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}
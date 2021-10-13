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

    public function index(Request $request): Response
    {
        //TODO: check referrer to route locales

        return $this->render('default/index.html.twig', $this->getCardsPaginator($request));
    }

    public function manage(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        return $this->render('_manage/default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/{_locale}/search", name="search")
     */
    public function search(Request $request) : Response
    {
        $isbn = $request->get('isbn');

        $cards = [];//$this->repository->findBy(['isbn' => $isbn]);

        return $this->render('default/search.html.twig', [
            'cards' => $cards,
        ]);
    }

    /**
     * @Route("/{_locale}/about", name="about")
     */
    public function about(): Response
    {
        return $this->render('default/about.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/{_locale}/donation", name="donation")
     */
    public function donation(): Response
    {
        return $this->render('default/donation.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    /**
     * @Route("/{_locale}/contact", name="contact")
     */
    public function contact(): Response
    {
        return $this->render('default/contact.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
    /**
     * @Route("/{_locale}/docs", name="docs")
     */
    public function docs(): Response
    {
        return $this->render('default/docs.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    //TODO: must be in trait or in abstract class
    private function getCardsPaginator(Request $request) : array
    {
        $page = $request->get('page', 1);
        $max = $request->get('max', 40);
        $first = ($page - 1) * $max;

        $query = $this->repository->createQueryWithPaginator($first, $max);

        $paginator = new Paginator($query, true);

        $c = count($paginator);

        $totalPages = (int)(($c + $max - 1) / $max);

        $pages = $c < $max ? [1] : range(1, $totalPages);

        return [
            'count' => $c,
            'pages' => $pages,
            'cards' => $paginator,
        ];
    }
}
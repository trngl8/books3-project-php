<?php

namespace App\Controller;

use App\Entity\Card;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Contracts\Translation\TranslatorInterface;

class DefaultController extends AbstractController
{
    private static $cookies = [
        'message1' =>  'Accept cookie policy',
        'message2' =>  'Accept personal data agreement',
        'message3' =>  'Read the docs',
        'message4' =>  'Test cards',
    ];

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
        $result = $this->getCardsPaginator($request);

        $response = $this->render('default/index.html.twig', $result);

        $response->headers->setCookie(Cookie::create('foo', 'bar'));

        return $response;
    }

    /**
     * @Route("/cards", name="cards")
     */
    public function cards(Request $request): Response
    {
        $result = $this->getCardsPaginator($request);

        $response = $this->render('default/cards.html.twig', $result);

        //TODO: get data to store cookie from request
        foreach (self::$cookies as $key => $cookie) {
            $response->headers->setCookie(Cookie::create($key, $cookie));
        }

        return $response;
    }

    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request) : Response
    {
        $isbn = $request->get('isbn');

        if(!$isbn) {
            return $this->redirectToRoute('homepage');
        }

        $cards = $this->repository->findBy(['isbn' => $isbn]);

        return $this->render('default/index.html.twig', [
            'cards' => $cards,
        ]);
    }

    /**
     * @Route("/messages", name="messages")
     */
    public function messages(Request $request, TranslatorInterface $translator): Response
    {
        $cookies = $request->cookies;

        $form = $this->createFormBuilder()
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $this->addFlash(
                'success',
                $translator->trans('flash.messages_cleared')
            );

            $response = $this->redirectToRoute('messages');

            foreach (self::$cookies as $key => $cookie) {
                $response->headers->clearCookie($key);
            }

            return $response;
        }

        $messages = [];

        foreach ($cookies as $key => $cookie) {
            if (in_array($key, array_keys(self::$cookies))) {
                $messages[$key] = $request->cookies->get($key);
            }
        }

        return $this->render('default/messages.html.twig', [
            'messages' => $messages,
            'form' => $form->createView(),
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
    /**
     * @Route("/docs", name="docs")
     */
    public function docs(): Response
    {
        return $this->render('default/docs.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }

    private function getCardsPaginator(Request $request) : array
    {
        $page = $request->get('page', 1);
        $max = $request->get('max', 20);
        $first = ($page - 1) * $max;

        $query = $this->repository->createQueryWithPaginator($first, $max);

        $paginator = new Paginator($query, true);

        $c = count($paginator);

        $pages = $c < $max ? [1] : range(1, intdiv($c, $max));

        return [
            'count' => $c,
            'pages' => $pages,
            'cards' => $paginator,
        ];
    }

}
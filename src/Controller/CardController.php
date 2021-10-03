<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CardController extends AbstractController
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
     * @Route("/cards/{id}", name="card_show")
     */
    public function index(Card $card): Response
    {
        return $this->render('card/show.html.twig', [
            'card' => $card,
        ]);
    }

    /**
     * @Route("/cards/{id}/order", name="card_order")
     */
    public function order(Card $card, Request $request): Response
    {
        $order = new Order();

        $form = $this->createFormBuilder($order)
            ->add('name', TextType::class, ['label' => 'form.label.name'])
            ->add('email', EmailType::class, ['label' => 'form.label.email'])
            ->add('save', SubmitType::class, ['label' => 'submit.save'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Order $order */
            $order = $form->getData();
            $order->addCard($card);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($order);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'flash.order_placed'
            );

            return $this->redirectToRoute('card_success', ['id' => $order->getId()]);
        }

        return $this->render('card/order.html.twig', [
            'card' => $card,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/orders/{id}/success", name="card_success")
     */
    public function success(Order $order, Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('city', TextType::class, ['label' => 'label.city'])
            ->add('number', IntegerType::class, ['label' => 'label.number'])
            ->add('name', TextType::class, ['label' => 'label.name'])
            ->add('save', SubmitType::class, ['label' => 'submit.save'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $options = $form->getData();

            $order->setOptions($options);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('order_checkout', ['id' => $order->getId()]);
        }

        return $this->render('card/success.html.twig', [
            'order' => $order,
            'form' => $form->createView(),
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

        $totalPages = (int)(($c + $max - 1) / $max);

        $pages = $c < $max ? [1] : range(1, $totalPages);

        return [
            'count' => $c,
            'pages' => $pages,
            'cards' => $paginator,
        ];
    }

}
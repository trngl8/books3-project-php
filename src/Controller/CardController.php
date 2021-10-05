<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Order;
use App\Entity\OrderItem;
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
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotNull;

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
     * @Route("/{_locale}/cards", name="cards")
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
     * @Route("/{_locale}/cards/{id}", name="card_show")
     */
    public function index(Card $card): Response
    {
        return $this->render('card/show.html.twig', [
            'card' => $card,
        ]);
    }

    /**
     * @Route("/{_locale}/cards/{id}/order", name="card_order")
     */
    public function order(Card $card, Request $request): Response
    {
        $values = [];
        if($user = $this->getUser()) {
            //TODO: get from profile
            $values['name'] = $user->getUserIdentifier();
            $values['email'] =$user->getUserIdentifier();
        }

        $form = $this->createFormBuilder($values)
            ->add('name', TextType::class, [
                'label' => 'form.label.name',
                'constraints' => [
                    new NotNull(),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'form.label.email',
                'constraints' => [
                    new NotNull(),
                    new Email()
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => 'label.phone',
                'constraints' => [
                    new NotNull(),
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'label.city',
                'constraints' => [
                    new NotNull(),
                ]
            ])
            ->add('number', IntegerType::class, [
                'label' => 'label.number',
                'constraints' => [
                    new NotNull(),
                ]
            ])
            ->add('save', SubmitType::class, ['label' => 'submit.save'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Order $order */
            $result = $form->getData();


            $order = (new Order())
                ->setEmail($result['email'])
                ->setName($result['name'])
                ->setOptions($result)
            ;

            $orderItem = (new OrderItem())
                ->setCard($card);
            ;

            $order->addItem($orderItem);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($order);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'flash.order_placed'
            );

            return $this->redirectToRoute('order_checkout', ['id' => $order->getId()]);

        }

        return $this->render('card/order.html.twig', [
            'card' => $card,
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
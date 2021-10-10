<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Order;
use App\Entity\OrderItem;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CardController extends AbstractController
{
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

        return $this->render('default/cards.html.twig', $result);

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
    public function order(Card $card, Request $request, MailerInterface $mailer, string $adminEmail): Response
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
                    new Assert\NotNull(),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'form.label.email',
                'constraints' => [
                    new Assert\NotNull(),
                    new Assert\Email()
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => 'label.phone',
                'constraints' => [
                    new Assert\NotNull(),
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'label.city',
                'constraints' => [
                    new Assert\NotNull(),
                ]
            ])
            ->add('number', IntegerType::class, [
                'label' => 'label.number',
                'constraints' => [
                    new Assert\NotNull(),
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

            //TODO: maybe notice
            $email = (new Email())
                ->from($adminEmail)
                ->to($adminEmail)
                ->priority(Email::PRIORITY_HIGH)
                ->subject('Order created')
                //->text(sprintf('To confirm book order you must click this link: %d', $path))
                ->html(sprintf('<p>Someone create new order: <a href="%s">see details</a></p>',
                    $this->generateUrl('orders_list', [], UrlGeneratorInterface::ABSOLUTE_URL)));

            $mailer->send($email);

            //$path = $this->generateUrl('orders_confirm', ['hash' => base64_encode(random_bytes(18)) ]);
            $path = $this->generateUrl('orders_confirm', ['hash' => $order->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

            $email = (new TemplatedEmail())
                ->from($adminEmail)
                ->to(new Address($order->getEmail()))
                ->priority(Email::PRIORITY_HIGH) //get from order type maybe
                ->subject(sprintf('Verify your book order #%s', $order->getId()))
                ->htmlTemplate('email/order_verify.html.twig')
                ->context([
                    'confirm_url' => $path,
                    'name' => $order->getName()
                ])
            ;

            $mailer->send($email);

            //TODO: put email to the users inbox

            $this->addFlash(
                'warning',
                'flash.please_confirm'
            );

            $response = $this->redirectToRoute('order_checkout', ['id' => $order->getId()]);

            $cookies = [
                'message1' =>  'message.please_confirm_order',
            ];

            foreach ($cookies as $key => $cookie) {
                $response->headers->setCookie(Cookie::create($key, $cookie));
            }

            return $response;
        }

        return $this->render('card/order.html.twig', [
            'card' => $card,
            'form' => $form->createView(),
        ]);
    }

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
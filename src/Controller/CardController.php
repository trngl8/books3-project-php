<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Inbox;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
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
     * @Route("/{_locale}/cards", name="cards", requirements={"_locale": "uk|en|ru|bg"})
     */
    public function cards(Request $request): Response
    {
        $result = $this->getCardsPaginator($request);

        return $this->render('default/cards.html.twig', $result);

    }

    /**
     * @Route("/{_locale}/cards/{id}", name="card_show", requirements={"_locale": "uk|en|ru|bg"})
     */
    public function index(Card $card): Response
    {
        return $this->render('card/show.html.twig', [
            'card' => $card,
        ]);
    }

    /**
     * @Route("/{_locale}/cards/{id}/order", name="card_order", requirements={"_locale": "uk|en|ru|bg"})
     */
    public function order(Card $card, ProfileRepository $repo, Request $request, MailerInterface $mailer, NotifierInterface $notifier, string $adminEmail): Response
    {
        $values = [];
        $user = $this->getUser();
        if($user) {
            $profile = $repo->findOneBy(['email' => $user->getUserIdentifier()]);
            $values['name'] = $profile->getName();
            $values['email'] = $profile->getEmail();
        }

        $form = $this->createFormBuilder($values)
            ->add('name', TextType::class, [
                'label' => 'form.label.name',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'form.label.email',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email()
                ]
            ])
            ->add('phone', TextType::class, [
                'label' => 'label.phone',
                'constraints' => [
                    new Assert\NotBlank(),
                ]
            ])
            ->add('city', TextType::class, [
                'label' => 'label.city',
                'constraints' => [
                    new Assert\NotBlank(),
                ]
            ])
            ->add('number', IntegerType::class, [
                'label' => 'label.number',
                'constraints' => [
                    new Assert\NotBlank(),
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

            $message = (new Inbox())
                ->setProvider('email')
                ->setAddress($order->getEmail()) //TODO: get from profile
                ->setSender($adminEmail)
                ->setSubject(sprintf('You ordered book #%s', $order->getId()))
                ->setText('message.please_confirm')
            ;
            $this->getDoctrine()->getManager()->persist($message);
            $this->getDoctrine()->getManager()->flush();

            $path = $this->generateUrl('orders_show', ['id' => $order->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

            $notification = (new Notification(sprintf('New order %s', $order->getId()), ['chat']))
                ->content(sprintf('[%s] Request to order book *%s* from %s. <%s|Details>', $order->getCreatedAt()->format('H:i:s'), $orderItem->getCard()->getTitle(), $order->getName(), $path));

            $recipient = new Recipient(
                $order->getEmail(), //TODO: get from profile
            );

            $notifier->send($notification, $recipient);

            $this->addFlash(
                'warning',
                'flash.please_confirm'
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
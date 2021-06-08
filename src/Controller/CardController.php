<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CardController extends AbstractController
{
    /**
     * @Route("/cards/{id}", name="card_show")
     */
    public function index(Card $card): Response
    {
        return $this->render('card/show.html.twig', [
            'card' => $card,
            'controller_name' => 'CardController',
        ]);
    }

    /**
     * @Route("/cards/{id}/order", name="card_order")
     */
    public function order(Card $card, Request $request): Response
    {
        $order = new Order();

        $form = $this->createFormBuilder($order)
            ->add('name', TextType::class)
            ->add('email', EmailType::class)
            ->add('save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Order $order */
            $order = $form->getData();
            $order->addCard($card);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('card_success', ['id' => $order->getId()]);
        }

        return $this->render('card/order.html.twig', [
            'card' => $card,
            'form' => $form->createView(),
            'controller_name' => 'CardController',
        ]);
    }

    /**
     * @Route("/orders/{id}/success", name="card_success")
     */
    public function success(Order $order, Request $request): Response
    {
        $form = $this->createFormBuilder()
            ->add('city', TextType::class)
            ->add('number', IntegerType::class)
            ->add('name', TextType::class)
            ->add('save', SubmitType::class)
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
            'controller_name' => 'CardController',
        ]);
    }

}
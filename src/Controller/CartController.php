<?php

namespace App\Controller;

use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart")
     */
    public function index(Request $request): Response
    {
        $order = new Order();
        $cards = [];

        $form = $this->createFormBuilder($order)
            ->add('name', TextType::class, ['label' => 'label.name'])
            ->add('email', EmailType::class, ['label' => 'label.email'])
            ->add('save', SubmitType::class, ['label' => 'submit.save'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Order $order */
            $order = $form->getData();
            //$order->addCard($card);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($order);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'flash.order_placed'
            );

            return $this->redirectToRoute('card_success', ['id' => $order->getId()]);
        }

        return $this->render('cart/index.html.twig', [
            'cards' => $cards,
            'form' => $form->createView(),
            'controller_name' => 'CartController',
        ]);
    }
}
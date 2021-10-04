<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/orders/{id}", name="order_show")
     */
    public function index(Card $card): Response
    {
        return $this->render('order/show.html.twig', [
            'card' => $card,
        ]);
    }

    /**
     * @Route("/orders/{id}/checkout", name="order_checkout")
     */
    public function checkout(Order $order): Response
    {
        return $this->render('order/checkout.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * @Route("/orders/{id}/success", name="order_success")
     */
    public function success(Order $order): Response
    {
        return $this->render('order/success.html.twig', [
            'order' => $order,
        ]);
    }

}
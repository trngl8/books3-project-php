<?php

namespace App\Controller;

use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/{_locale}/orders/{id}/checkout", name="order_checkout")
     */
    public function checkout(Order $order): Response
    {
        return $this->render('order/checkout.html.twig', [
            'order' => $order,
        ]);
    }

}
<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

    /**
     * @Route("/orders", name="orders_list")
     */
    public function list(OrderRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        //TODO: check owner
        $orders = $repo->findBy([], ['createdAt' => 'DESC']);

        return $this->render('order/list.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("/orders/{id}/show", name="orders_show")
     */
    public function show(Order $order): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        //TODO: check owner
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * @Route ("/orders/{hash}/confirm", name="orders_confirm")
     */
    public function confirm(string $hash, OrderRepository $repo)
    {
        $order = $repo->findOneBy(['id' => $hash]);

        if(!$order) {
            throw new NotFoundHttpException(sprintf("Order with id %s not found", $hash));
        }

        $order->setStatus('confirmed');
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Order confirmed');

        return $this->redirectToRoute('orders_list');

    }

}
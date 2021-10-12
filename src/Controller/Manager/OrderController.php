<?php

namespace App\Controller\Manager;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/manager", name="manager_")
 */
class OrderController extends AbstractController
{
    /**
     * @Route("/orders", name="orders_list")
     */
    public function list(OrderRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

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
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * @Route("/orders/{id}/remove", name="orders_remove")
     */
    public function remove(Order $order, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('remove', $submittedToken)) {
            $this->getDoctrine()->getManager()->remove($order);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'flash.order.removed');

            return $this->redirectToRoute('manager_orders_list');
        }

        return $this->render('order/remove.html.twig', [
            'order' => $order,
        ]);
    }
}
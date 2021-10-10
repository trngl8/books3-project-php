<?php

namespace App\Controller;

use App\Entity\Slot;
use App\Repository\OrderRepository;
use App\Repository\SlotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class SlotController extends AbstractController
{
    /**
     * @Route("/events", name="slots_list")
     */
    public function list(SlotRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $slots = $repo->findBy([], ['createdAt' => 'DESC']);

        return $this->render('slot/list.html.twig', [
            'slots' => $slots,
        ]);
    }

    /**
     * @Route("/events/{id}/show", name="slots_show")
     */
    public function show(Slot $slot): Response
    {
        return $this->render('slot/show.html.twig', [
            'slot' => $slot,
        ]);
    }

    /**
     * @Route("/events/{id}/remove", name="slots_remove")
     */
    public function remove(Slot $slot, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('remove', $submittedToken)) {
            $this->getDoctrine()->getManager()->remove($slot);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Slot removed');

            return $this->redirectToRoute('slots_list');
        }

        return $this->render('slot/remove.html.twig', [
            'slot' => $slot,
        ]);
    }

    /**
     * @Route ("/slot/{hash}/subscribe", name="slots_subscribe")
     */
    public function subscribe(string $hash, SlotRepository $repo)
    {
        $slot = $repo->findOneBy(['id' => $hash]);

        if(!$slot) {
            throw new NotFoundHttpException(sprintf("Slot with id %s not found", $hash));
        }

        //TODO: add subscription process

        $this->addFlash('success', 'User subscribed');

        return $this->redirectToRoute('slots_list');
    }

    /**
     * @Route ("/slot/{id}/order", name="slots_order")
     */
    public function order(Slot $slot): Response
    {
        $slot->setStatus('ordered');
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Ordered');

        $this->addFlash(
            'warning',
            'flash.please_confirm'
        );

        $response = $this->redirectToRoute('profile_show', ['id' => $slot->getOwner()->getId()]);

        $cookies = [
            'message2' =>  'message.please_confirm_slot_order',
        ];

        foreach ($cookies as $key => $cookie) {
            $response->headers->setCookie(Cookie::create($key, $cookie));
        }

        return $response;
    }

    /**
     * @Route ("/slot/{hash}/confirm", name="slots_confirm")
     */
    public function confirm(string $hash, OrderRepository $repo)
    {
        $slot = $repo->findOneBy(['id' => $hash]);

        if(!$slot) {
            throw new NotFoundHttpException(sprintf("Slot with id %s not found", $hash));
        }

        $slot->setStatus('confirmed');
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Slot confirmed');

        return $this->redirectToRoute('slots_list');
    }
}
<?php

namespace App\Controller\Manager;

use App\Entity\Slot;
use App\Repository\SlotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/manager", name="manager_")
 */
class SlotController extends AbstractController
{
    /**
     * @Route("/events", name="slots_list")
     */
    public function list(SlotRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $slots = $repo->findBy([], ['createdAt' => 'DESC']);

        return $this->render('_manage/slot/list.html.twig', [
            'slots' => $slots,
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

            return $this->redirectToRoute('manager_slots_list');
        }

        return $this->render('slot/remove.html.twig', [
            'slot' => $slot,
        ]);
    }


}
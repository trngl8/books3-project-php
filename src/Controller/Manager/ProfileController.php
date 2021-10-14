<?php

namespace App\Controller\Manager;

use App\Entity\Profile;
use App\Form\ProfileManagerType;
use App\Form\ProfileType;
use App\Repository\ProfileRepository;
use App\Repository\SlotRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/manager/profiles", name="manager_profiles_list")
     */
    public function list(ProfileRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $profiles = $repo->findBy([]);

        return $this->render('_manage/profile/list.html.twig', [
            'profiles' => $profiles,
        ]);
    }

    /**
     * @Route("/manager/profiles/{id}/edit", name="manager_profiles_edit")
     */
    public function edit(Profile $profile, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $form = $this->createForm(ProfileManagerType::class, $profile);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash(
                'success',
                'message.profile.updated'
            );

            return $this->redirectToRoute('manager_profiles_list');
        }

        return $this->render('_manage/profile/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/manager/profiles/{id}/show", name="manager_profiles_show")
     */
    public function show(Profile $profile, SlotRepository $slotRepo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $events = $slotRepo->findBy(['owner' => $profile]);

        return $this->render('_manage/profile/show.html.twig', [
            'profile' => $profile,
            'events' => $events
        ]);
    }
}
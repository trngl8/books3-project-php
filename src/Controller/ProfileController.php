<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\Slot;
use App\Form\ChangePasswordType;
use App\Form\ProfileType;
use App\Form\SlotType;
use App\Repository\ProfileRepository;
use App\Repository\SlotRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    /**
     * @Route("/profile", name="profile")
     */
    public function profile(Request $request, ProfileRepository $profileRepo, SlotRepository $slotRepo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        $profile = $profileRepo->findOneBy(['email' => $user->getUserIdentifier()]);

        $events = $slotRepo->findBy(['owner' => $user]);

        $slot = new Slot();
        $form = $this->createForm(SlotType::class, $slot);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $slot->setOwner($profile);
            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($slot);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'message.event_created'
            );

            return $this->redirectToRoute('profile');
        }

        return $this->render('account/profile.html.twig', [
            'form' => $form->createView(),
            'profile' => $profile,
            'events' => $events
        ]);
    }

    /**
     * @Route("/slots/{id}/show", name="slots_show")
     */
    public function slots(Slot $slot): Response
    {
        return $this->render('slot/show.html.twig', [
            'slot' => $slot
        ]);
    }

    /**
     * @Route("/profile/edit", name="profile_edit")
     */
    public function edit(Request $request, ProfileRepository $repo): Response
    {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); //TODO: maybe redirect to password enter

        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        $profile = $repo->findOneBy(['email' => $user->getUserIdentifier()]);

        $form = $this->createForm(ProfileType::class, $profile);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash(
                'success',
                'message.profile.updated'
            );

            return $this->redirectToRoute('profile');
        }

        return $this->render('account/profile_edit.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/change-password", name="change_password")
     */
    public function changePassword(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(ChangePasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();
            if(!$user) {
                $this->addFlash(
                    'danger',
                    'message.unknown_user'
                );
                //TODO: move to danger zone
                return $this->redirectToRoute('profile');
            }

            $data = $form->getData();

            $user = $userRepository->findOneBy(['username' => $user->getUserIdentifier()]);
            $user->setPassword($passwordHasher->hashPassword(
                $user,
                $data['password']
            ));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            $this->addFlash(
                'success',
                'message.changes_saved'
            );

            return $this->redirectToRoute('profile');

        }

        return $this->render('account/change_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/profile/{id}/show", name="profile_show")
     */
    public function show(Profile $profile, SlotRepository $slotRepo): Response
    {
        $events = $slotRepo->findBy(['owner' => $profile]);

        return $this->render('account/profile.html.twig', [
            'profile' => $profile,
            'events' => $events
        ]);
    }
}
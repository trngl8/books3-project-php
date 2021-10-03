<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Form\ProfileType;
use App\Repository\ProfileRepository;
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
    public function profile(ProfileRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $profile = $repo->findOneBy(['email' => $user->getUserIdentifier()]);

        return $this->render('account/profile.html.twig', [
            'profile' => $profile
        ]);
    }

    /**
     * @Route("/profile/edit", name="profile_edit")
     */
    public function edit(Request $request, ProfileRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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
}
<?php

namespace App\Controller;

use App\Entity\Invite;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class InviteController extends AbstractController
{
    private $haser;

    public function __construct(UserPasswordEncoderInterface $hasher)
    {
        $this->haser = $hasher;
    }

    /**
     * @Route("/invite/{id}/accept", name="invite_accept")
     */
    public function index(Invite $invite, Request $request): Response
    {
        $form = $this->createFormBuilder(['email' => $invite->getEmail()])
            ->add('email', EmailType::class, ['label' => 'label.email', 'attr' => [
                'readonly' => true,
            ]])
            ->add('password', RepeatedType::class, ['type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'label.password'],
                'second_options' => ['label' => 'label.repeat_password']
            ])
            ->add('save', SubmitType::class, ['label' => 'submit.save'])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            //TODO: choose variation in user exists
            $data = $form->getData();

            $user = (new User())
                ->setEmail($data['email'])
                ->setRoles(['ROLE_ADMIN'])
            ;

            $encodedPassword = $this->haser->encodePassword($user, $data['password']);

            $user->setPassword($encodedPassword);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'flash.invite_confirmed'
            );

            return $this->redirectToRoute('login');
        }

        return $this->render('invite/accept.html.twig', [
            'invite' => $invite,
            'form' => $form->createView(),
            'controller_name' => 'InviteController',
        ]);
    }


}
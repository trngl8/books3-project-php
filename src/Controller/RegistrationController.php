<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Profile;
use App\Entity\User;
use App\Model\Registration;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private $emailVerifier;

    private $adminEmail;

    private $em;

    public function __construct(EmailVerifier $emailVerifier, EntityManagerInterface $em, string $adminEmail)
    {
        $this->emailVerifier = $emailVerifier;
        $this->adminEmail = $adminEmail;
        $this->em = $em;
    }

    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $registration = new Registration();
        $form = $this->createForm(RegistrationFormType::class, $registration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //TODO: maybe put object into constructor
            $subscription = (new Account())
                ->setName($registration->getUsername())
                ->setActive(false)
                ->setCreatedBy($registration->getUsername())
            ;

            $profile = (new Profile())
                ->setName($registration->getUsername())
                ->setEmail($registration->getUsername())
                ->setLocale($request->getLocale())
                ->setTimezone('Europe\London')
            ;

            $user = (new User())
                ->setUsername($registration->getUsername())
            ;

            $this->em->persist($user);
            $this->em->persist($subscription);
            $this->em->persist($profile);

            //TODO: move $passwordEncoder to service
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $this->em->flush();

            try {
                $this->emailVerifier->sendEmailConfirmation('verify_email', $user,
                    (new TemplatedEmail())
                        ->from(new Address($this->adminEmail, 'Project Name or Owner Name'))
                        ->to($user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                );
            } catch (TransportExceptionInterface $exception) {
                //TODO:
                $this->addFlash('danger', $exception->getMessage());

                return $this->redirectToRoute('homepage');
            }

            //TODO: send detailed flash
            //TODO: message to config
            $this->addFlash('success', 'Your subscribed successfully. Please confirm your mail to complete subscribe.');

            return $this->redirectToRoute('account');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        //TODO: verify user
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('danger', $exception->getReason());

            return $this->redirectToRoute('account');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('account');
    }
}
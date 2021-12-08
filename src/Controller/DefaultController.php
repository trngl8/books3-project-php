<?php

namespace App\Controller;

use App\Form\DonationType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function index(): Response
    {
        return $this->forward('App\Controller\CardController::index');
    }

    public function manage(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        return $this->render('_manage/default/index.html.twig');
    }

    /**
     * @Route("/{_locale}/search", name="search", requirements={"_locale": "uk|en|ru|bg"})
     */
    public function search(Request $request) : Response
    {
        //TODO: implement search by params and keywords
        $isbn = $request->get('isbn');

        $cards = [];//$this->repository->findBy(['isbn' => $isbn]);

        return $this->render('default/search.html.twig', [
            'cards' => $cards,
        ]);
    }

    /**
     * @Route("/{_locale}/about", name="about", requirements={"_locale": "uk|en|ru|bg"})
     */
    public function about(): Response
    {
        return $this->render('default/about.html.twig');
    }

    /**
     * @Route("/{_locale}/donation", name="donation", requirements={"_locale": "uk|en|ru|bg"})
     */
    public function donation(Request $request, MailerInterface $mailer, string $adminEmail): Response
    {
        $form = $this->createForm(DonationType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $donation = $form->getData();

            $email = (new TemplatedEmail())
                ->from($donation->email)
                ->to(new Address($adminEmail))
                ->priority(Email::PRIORITY_HIGH)
                ->subject(sprintf('Donation request from #%s', $donation->email))
                ->htmlTemplate('email/donation_request.html.twig')
                ->context([
                    'name' => $donation->name,
                    'mail' => $donation->email,
                    'text' => $donation->text
                ])
            ;

            $mailer->send($email);
            $this->addFlash('success', 'message.donation_added');

            $this->redirectToRoute('donation');
        }
        return $this->render('default/donation.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{_locale}/contact", name="contact", requirements={"_locale": "uk|en|ru|bg"})
     */
    public function contact(): Response
    {
        return $this->render('default/contact.html.twig');
    }

    /**
     * @Route("/{_locale}/docs", name="docs", requirements={"_locale": "uk|en|ru|bg"})
     */
    public function docs(): Response
    {
        return $this->render('default/docs.html.twig');
    }

    /**
     * @Route("/thankyoy", name="thankyoy")
     */
    public function success(): Response
    {
        //TODO: implement params checkers

        return $this->render('default/success.html.twig');
    }
}
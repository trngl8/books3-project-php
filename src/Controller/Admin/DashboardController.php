<?php

namespace App\Controller\Admin;

use App\Entity\Card;
use App\Entity\Loan;
use App\Entity\Member;
use App\Entity\Rescript;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\Mime\Email;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $loans = $this->getDoctrine()->getRepository(Loan::class)->findExpired(new \DateTime());

        return $this->render('admin/dashboard.html.twig', [
            'dashboard_controller_filepath' => (new \ReflectionClass(static::class))->getFileName(),
            'dashboard_controller_class' => (new \ReflectionClass(static::class))->getShortName(),
            'loans' => $loans
        ]);
    }

    /**
     * @Route("/admin/loans/{id}/remind", name="admin_loans_remind")
     * @ParamConverter("loan", class="App:Loan")
     */
    public function remind(Loan $loan, MailerInterface $mailer, string $adminEmail): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $date = (new \DateTime($loan->getFinishAt()->format('Y-m-d')))->modify('+1 month');

        $loan->setFinishAt($date);

        $email = (new Email())
            ->from($adminEmail)
            ->to($loan->getMember()->getEmail())
            ->subject('Please return the book!')
            ->text(sprintf('Please return the book %s', $loan->getRescript()->getCard()->getTitle()))
            ->html(sprintf('<p>Please return the book <b>%s</b></p>', $loan->getRescript()->getCard()->getTitle()));

        $mailer->send($email);

        $this->addFlash('primary', sprintf('Loan %d reminded. Next expire: %s', $loan->getId(), $loan->getFinishAt()->format('Y-m-d')));

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('admin_dashboard');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Booking');
    }

    public function configureMenuItems(): iterable
    {
        //yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);

        return [
            MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),

            MenuItem::section('Booking'),
            MenuItem::linkToCrud('Cards', 'fa fa-tags', Card::class),
            MenuItem::linkToCrud('Members', 'fa fa-user', Member::class),
            MenuItem::linkToCrud('Rescripts', 'fas fa-clone', Rescript::class),
            MenuItem::linkToCrud('Loans', 'fas fa-sign-language', Loan::class),

            MenuItem::section('Users'),
            //MenuItem::linkToCrud('Users', 'fa fa-user', User::class),
        ];
    }
}
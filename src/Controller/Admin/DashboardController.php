<?php

namespace App\Controller\Admin;

use App\Entity\Card;
use App\Entity\Invite;
use App\Entity\Loan;
use App\Entity\Member;
use App\Entity\Order;
use App\Entity\Rescript;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
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

        return $this->render('admin_dashboard.html.twig');

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
            ->setTitle('Booking')
            ->disableUrlSignatures()
        ;

    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            // this defines the pagination size for all CRUD controllers
            // (each CRUD controller can override this value if needed)
            ->setPaginatorPageSize(20)

            ;
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
            MenuItem::linkToCrud('Orders', 'fas fa-bookmark', Order::class),
            MenuItem::linkToCrud('Invites', 'fas fa-user-plus', Invite::class),

            MenuItem::section('Users'),
            //MenuItem::linkToCrud('Users', 'fa fa-user', User::class),
        ];
    }
}
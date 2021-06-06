<?php

namespace App\EventListener;

use App\Entity\Loan;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class LoanVerification
{
    private MailerInterface $mailer;

    private string $adminEmail;

    public function __construct(MailerInterface $mailer, string $adminEmail)
    {
        $this->mailer = $mailer;

        $this->adminEmail = $adminEmail;
    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Loan) {
            return;
        }

        $member = $entity->getMember();

        if (!$member) {
            throw new \Exception('Invalid member');
        }

        $email = $member->getEmail();
        $code = rand(1000, 9999);

        $email = (new Email())
            ->from($this->adminEmail)
            ->to($email)
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('reply@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Verify your book order')
            ->text(sprintf('To confirm book order you must send this code: %d', $code))
            ->html(sprintf('<p>To confirm book order you must send this code: <b>%d</b></p>', $code));

        $this->mailer->send($email);
    }
}
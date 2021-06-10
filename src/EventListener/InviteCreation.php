<?php

namespace App\EventListener;

use App\Entity\Invite;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;

class InviteCreation
{
    private MailerInterface $mailer;
    private Security $security;


    private string $adminEmail;

    public function __construct(MailerInterface $mailer, Security $security, string $adminEmail)
    {
        $this->mailer = $mailer;
        $this->security = $security;

        $this->adminEmail = $adminEmail;
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Invite) {
            return;
        }

        $user = $this->security->getUser();
        $entity->setCreatedBy($user->getUsername());

        //TODO: set text template
        $email = (new TemplatedEmail())
            ->from($this->adminEmail)
            ->to($entity->getEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('reply@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Invite for you')
            //->text(sprintf('%s sent invite for you. Please follow link %s', $entity->getCreatedBy(), (string)$entity->getId()))
            ->htmlTemplate('email/invite.html.twig')
            ->context([
                'invite' => $entity,
            ])
        ;

        $this->mailer->send($email);
    }
}
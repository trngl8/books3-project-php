<?php

namespace App\Controller;

use App\Entity\Inbox;
use App\Entity\Slot;
use App\Entity\Subscription;
use App\Repository\ProfileRepository;
use App\Repository\SlotRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class SlotController extends AbstractController
{
    /**
     * @Route("/events", name="slots_list")
     */
    public function list(SlotRepository $repo): Response
    {
        $slots = $repo->findBy(['publicActive' => true], ['createdAt' => 'DESC']);

        return $this->render('slot/list.html.twig', [
            'slots' => $slots,
        ]);
    }

    /**
     * @Route("/events/{id}/show", name="slots_show")
     */
    public function show(Slot $slot): Response
    {
        if($slot->getPublicActive() || !$this->isGranted('ROLE_MANAGER')) {
            throw new \RuntimeException(sprintf("Slot is inactive"));
        }

        return $this->render('slot/show.html.twig', [
            'slot' => $slot,
        ]);
    }

    /**
     * @Route ("/slot/{id}/order", name="slots_order")
     */
    public function order(ProfileRepository $repository, Slot $slot, MailerInterface $mailer, string $adminEmail): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $slot->setStatus('ordered');

        $user = $this->getUser();

        $profile = $repository->findOneBy(['email' => $user->getUserIdentifier()]);

        $owner = $slot->getOwner();

        $subscription = (new Subscription('slot', $slot->getUuid()))
            ->setProfile($profile)
        ;

        $this->getDoctrine()->getManager()->persist($subscription);
        $this->getDoctrine()->getManager()->flush();

        //Message to the owner for the first
        $email = (new TemplatedEmail())
            ->from($adminEmail)
            ->to(new Address($owner->getEmail()))
            ->priority(Email::PRIORITY_HIGH) //get from order type or slot status
            ->subject(sprintf('User ordered slot #%s', $slot->getId()))
            ->htmlTemplate('email/slot_ordered.html.twig')
            ->context([
                'name' => $owner->getName(),
                'username' => $user->getUserIdentifier(),
                'slot' => $slot
            ])
        ;

        $mailer->send($email);

        $this->addFlash('success', 'flash.ordered');

        //And message to subscriber to confirm
        $email = (new TemplatedEmail())
            ->from($adminEmail)
            ->to(new Address($profile->getEmail()))
            ->priority(Email::PRIORITY_HIGH) //get from order type or slot status
            ->subject(sprintf('You ordered slot #%s', $slot->getId()))
            ->htmlTemplate('email/confirm_subscription.html.twig')
            ->context([
                'name' => $profile->getName(),
                'hash' => $slot->getUuid(),
                'slot' => $slot
            ])
        ;

        $mailer->send($email);

        $this->addFlash('warning','flash.please_confirm');

        $message = (new Inbox())
            ->setProvider('email')
            ->setSender($adminEmail)
            ->setSubject(sprintf('You ordered slot #%s', $slot->getId())) //TODO: translate this
            ->setText('message.please_confirm_slot_order')
        ;

        $this->getDoctrine()->getManager()->persist($message);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('profile_show', ['id' => $owner->getId()]);
    }

    /**
     * @Route ("/slot/{hash}/confirm", name="slots_confirm")
     */
    public function confirm(string $hash, SlotRepository $repo)
    {
        $slot = $repo->findOneBy(['uuid' => $hash]);

        if(!$slot) {
            throw new NotFoundHttpException(sprintf("Slot with id %s not found", $hash));
        }

        $slot->setStatus('confirmed');
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', 'Slot confirmed');

        return $this->redirectToRoute('slots_list');
    }
}
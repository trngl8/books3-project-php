<?php

namespace App\DataFixtures;

use App\Entity\Card;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CardFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $card = (new Card())
            ->setTitle('Test Title')
            ->setAuthor('test@test.com')
            ->setYear((new \DateTime())->format('Y'))
            ->setDescription('Book description')
            ->setIsbn('test')
            ->setLanguage('ua')
            ->setPublisher('MegaPublisher Inc.')
        ;

        $manager->persist($card);

        $manager->flush();
    }
}
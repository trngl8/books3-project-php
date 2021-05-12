<?php

namespace App\Tests\Service;

use App\Entity\Card;
use App\Entity\Member;
use App\Service\Scriptorium;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ScriptoriumTest extends KernelTestCase
{
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testBorrowSuccess(): void
    {
        $member = $this->entityManager->getRepository(Member::class)->findOneBy([]);
        $card = $this->entityManager->getRepository(Card::class)->findOneBy([]);

        $scriptorium = new Scriptorium();
        $result = $scriptorium->borrow($member, $card);
        $this->assertTrue($result);
    }

    public function testBorrowFailMember(): void
    {
        $member = $this->entityManager->getRepository(Member::class)->findOneBy([]);
        $card = $this->entityManager->getRepository(Card::class)->findOneBy([]);

        $scriptorium = new Scriptorium();

        $member->setName('');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(sprintf('Can not borrow card %s with empty member #%d name', $card->getId(), $member->getId()));

        $scriptorium->borrow($member, $card);
    }

    public function testBorrowFailCard(): void
    {
        $member = $this->entityManager->getRepository(Member::class)->findOneBy([]);
        $card = $this->entityManager->getRepository(Card::class)->findOneBy([]);

        $scriptorium = new Scriptorium();

        $card->setTitle('');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(sprintf('Can not borrow card %s with empty title', $card->getId()));

        $scriptorium->borrow($member, $card);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
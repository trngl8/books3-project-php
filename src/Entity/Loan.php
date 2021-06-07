<?php

namespace App\Entity;

use App\Repository\LoanRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LoanRepository::class)
 */
class Loan
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Rescript::class, inversedBy="loans")
     * @ORM\JoinColumn(nullable=false)
     */
    private $rescript;

    /**
     * @ORM\ManyToOne(targetEntity=Member::class, inversedBy="loans")
     * @ORM\JoinColumn(nullable=false)
     */
    private $member;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $finishAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $closedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRescript(): ?Rescript
    {
        return $this->rescript;
    }

    public function setRescript(?Rescript $rescript): self
    {
        $this->rescript = $rescript;

        return $this;
    }

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(?Member $member): self
    {
        $this->member = $member;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getFinishAt(): ?\DateTimeInterface
    {
        return $this->finishAt;
    }

    public function setFinishAt(\DateTimeInterface $finishAt): self
    {
        $this->finishAt = $finishAt;

        return $this;
    }

    public function getClosedAt(): ?\DateTimeInterface
    {
        return $this->closedAt;
    }

    public function setClosedAt(?\DateTimeInterface $closedAt): self
    {
        $this->closedAt = $closedAt;

        return $this;
    }
}

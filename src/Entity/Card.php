<?php

namespace App\Entity;

use App\Repository\CardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CardRepository::class)
 * @ApiResource()
 * @ORM\HasLifecycleCallbacks()
 */
class Card
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     *
     * @Groups({"card:list", "card:item"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"card:list", "card:item"})
     */
    private string $title;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"card:list", "card:item"})
     */
    private string $author;

    /**
     * @ORM\Column(type="integer")
     */
    private int $year;

    /**
     * @ORM\Column(type="string", length=13, nullable=true)
     * @Assert\Isbn()
     */
    private ?string $isbn;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private ?string $publisher;

    /**
     * @ORM\Column(type="string", length=8, nullable=true)
     */
    private ?string $type;

    /**
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private ?string $language;

    /**
     * @ORM\OneToMany(targetEntity=Rescript::class, mappedBy="card")
     */
    private $rescripts;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $publishing;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $copyrights;

    /**
     * @ORM\OneToMany(targetEntity=OrderItem::class, mappedBy="card")
     */
    private $orderItems;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $createdBy;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $updatedBy;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $cover;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $coverFilename;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    public function __construct(?string $uuidValue = null)
    {
        if(!$this->id) {
            $this->id = $uuidValue ? Uuid::fromString($uuidValue) : Uuid::v4();
        }

        $this->createdAt = new \DateTimeImmutable();

        $this->rescripts = new ArrayCollection();
        $this->orderItems = new ArrayCollection();
        $this->active = false;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function updateTimestamps()
    {
        $this->updatedAt = new \DateTime();
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(?string $publisher): self
    {
        $this->publisher = $publisher;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return Collection|Rescript[]
     */
    public function getRescripts(): Collection
    {
        return $this->rescripts;
    }

    public function addRescript(Rescript $rescript): self
    {
        if (!$this->rescripts->contains($rescript)) {
            $this->rescripts[] = $rescript;
            $rescript->setCard($this);
        }

        return $this;
    }

    public function removeRescript(Rescript $rescript): self
    {
        if ($this->rescripts->removeElement($rescript)) {
            // set the owning side to null (unless already changed)
            if ($rescript->getCard() === $this) {
                $rescript->setCard(null);
            }
        }

        return $this;
    }

    public function getPublishing(): ?string
    {
        return $this->publishing;
    }

    public function setPublishing(?string $publishing): self
    {
        $this->publishing = $publishing;

        return $this;
    }

    public function getCopyrights(): ?string
    {
        return $this->copyrights;
    }

    public function setCopyrights(?string $copyrights): self
    {
        $this->copyrights = $copyrights;

        return $this;
    }

    public function setCover(?string $cover): self
    {
        $this->cover = $cover;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    /**
     * @return Collection|OrderItem[]
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): self
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems[] = $orderItem;
            $orderItem->setCard($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): self
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getCard() === $this) {
                $orderItem->setCard(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getCoverFilename(): ?string
    {
        return $this->coverFilename;
    }

    public function setCoverFilename(?string $coverFilename): self
    {
        $this->coverFilename = $coverFilename;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active == true;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
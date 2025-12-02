<?php

namespace App\Entity;

use App\Repository\LeaseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: LeaseRepository::class)]
class Lease
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column]
    private ?bool $returned = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'leases')]
    private ?Item $item = null;

    #[ORM\ManyToOne(inversedBy: 'leases')]
    private ?User $lessee = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $leased_from = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $leased_to = null;

    public function __construct()
    {
        $this->leased_from = new \DateTime();
        $this->leased_to = (new \DateTime())->modify('+7 days');
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function isReturned(): ?bool
    {
        return $this->returned;
    }

    public function setReturned(bool $returned): static
    {
        $this->returned = $returned;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): static
    {
        $this->item = $item;

        return $this;
    }

    public function getLessee(): ?User
    {
        return $this->lessee;
    }

    public function setLessee(?User $lessee): static
    {
        $this->lessee = $lessee;

        return $this;
    }

    public function getLeasedFrom(): ?\DateTime
    {
        return $this->leased_from;
    }

    public function setLeasedFrom(\DateTime $leased_from): static
    {
        $this->leased_from = $leased_from;

        return $this;
    }

    public function getLeasedTo(): ?\DateTime
    {
        return $this->leased_to;
    }

    public function setLeasedTo(\DateTime $leased_to): static
    {
        $this->leased_to = $leased_to;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CustomerRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\Table(name: 'Customers')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['name'], message: 'A customer with this name already exists.')]
class Customer extends SoftDeletableEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'Id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'CustomersName', type: 'string', length: 50, nullable: true, unique: true)]
    #[Assert\Length(max: 50)]
    private ?string $name;

    #[ORM\Column(name: 'CustomersCreatedBy', type: 'integer')]
    private int $createdById;

    #[ORM\Column(name: 'CustomersCreated', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'CustomersUpdated', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(name: 'AgenciesIdAgencies', type: 'integer', nullable: true)]
    private ?int $agencyId = null;

    #[ORM\Column(name: 'mergedWith', type: 'integer', nullable: true)]
    private ?int $mergedWithId = null;

    public function __construct(?string $name, int $createdById)
    {
        $this->name = $name === null ? null : trim($name);
        $this->createdById = $createdById;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name === null ? null : trim($name);

        return $this;
    }

    public function getCreatedById(): int
    {
        return $this->createdById;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getAgencyId(): ?int
    {
        return $this->agencyId;
    }

    public function setAgencyId(?int $agencyId): self
    {
        $this->agencyId = $agencyId;

        return $this;
    }

    public function getMergedWithId(): ?int
    {
        return $this->mergedWithId;
    }

    public function setMergedWithId(?int $mergedWithId): self
    {
        $this->mergedWithId = $mergedWithId;

        return $this;
    }

    #[ORM\PreUpdate]
    public function touchUpdatedAt(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}

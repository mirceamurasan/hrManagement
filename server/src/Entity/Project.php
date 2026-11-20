<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProjectRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\Table(name: 'Projects')]
#[ORM\HasLifecycleCallbacks]
class Project extends SoftDeletableEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'Id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    #[ORM\JoinColumn(name: 'CustomersIdCustomers', referencedColumnName: 'Id', nullable: false)]
    private Customer $customer;

    #[ORM\ManyToOne(targetEntity: ProjectStatus::class)]
    #[ORM\JoinColumn(name: 'ProjectStatus', referencedColumnName: 'Id', nullable: true)]
    private ?ProjectStatus $status = null;

    #[ORM\Column(name: 'ProjectsCreatedBy', type: 'integer')]
    private int $createdById;

    #[ORM\Column(name: 'ProjectsName', type: 'string', length: 250, nullable: true)]
    #[Assert\Length(max: 250)]
    private ?string $name;

    #[ORM\Column(name: 'ProjectsComments', type: 'text', nullable: true)]
    private ?string $comments = null;

    #[ORM\Column(name: 'ProjectsCreated', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'ProjectsUpdated', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(name: 'ProjectFirstTaskDate', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $firstTaskDate = null;

    #[ORM\Column(name: 'ProjectLastTaskDate', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $lastTaskDate = null;

    #[ORM\Column(name: 'ProjectsSuccess', type: 'integer')]
    private int $success = 0;

    #[ORM\Column(name: 'isOutsourcing', type: 'boolean')]
    private bool $outsourcing = false;

    #[ORM\Column(name: 'SapNumber', type: 'string', length: 50, nullable: true)]
    private ?string $sapNumber = null;

    #[ORM\Column(name: 'InfoGeneral', type: 'string', length: 50, nullable: true)]
    private ?string $generalInfo = null;

    #[ORM\Column(name: 'InfoTech', type: 'string', length: 50, nullable: true)]
    private ?string $technicalInfo = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    #[ORM\JoinColumn(name: 'mergedWith', referencedColumnName: 'Id', nullable: true)]
    private ?self $mergedWith = null;

    public function __construct(Customer $customer, int $createdById, ?string $name = null)
    {
        $this->customer = $customer;
        $this->createdById = $createdById;
        $this->name = $name === null ? null : trim($name);
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getStatus(): ?ProjectStatus
    {
        return $this->status;
    }

    public function setStatus(?ProjectStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedById(): int
    {
        return $this->createdById;
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

    public function getComments(): ?string
    {
        return $this->comments;
    }

    public function setComments(?string $comments): self
    {
        $this->comments = $comments;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getFirstTaskDate(): ?DateTimeImmutable
    {
        return $this->firstTaskDate;
    }

    public function setFirstTaskDate(?DateTimeImmutable $date): self
    {
        $this->firstTaskDate = $date;

        return $this;
    }

    public function getLastTaskDate(): ?DateTimeImmutable
    {
        return $this->lastTaskDate;
    }

    public function setLastTaskDate(?DateTimeImmutable $date): self
    {
        $this->lastTaskDate = $date;

        return $this;
    }

    public function getSuccess(): int
    {
        return $this->success;
    }

    public function setSuccess(int $success): self
    {
        $this->success = $success;

        return $this;
    }

    public function isOutsourcing(): bool
    {
        return $this->outsourcing;
    }

    public function setOutsourcing(bool $outsourcing): self
    {
        $this->outsourcing = $outsourcing;

        return $this;
    }

    public function getSapNumber(): ?string
    {
        return $this->sapNumber;
    }

    public function setSapNumber(?string $sapNumber): self
    {
        $this->sapNumber = $sapNumber === null ? null : trim($sapNumber);

        return $this;
    }

    public function getGeneralInfo(): ?string
    {
        return $this->generalInfo;
    }

    public function setGeneralInfo(?string $generalInfo): self
    {
        $this->generalInfo = $generalInfo;

        return $this;
    }

    public function getTechnicalInfo(): ?string
    {
        return $this->technicalInfo;
    }

    public function setTechnicalInfo(?string $technicalInfo): self
    {
        $this->technicalInfo = $technicalInfo;

        return $this;
    }

    public function getMergedWith(): ?self
    {
        return $this->mergedWith;
    }

    public function setMergedWith(?self $project): self
    {
        $this->mergedWith = $project;

        return $this;
    }

    #[ORM\PreUpdate]
    public function touchUpdatedAt(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}

<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProjectStatusRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectStatusRepository::class)]
#[ORM\Table(name: 'ProjectStatuses')]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['label'], message: 'A project status with this label already exists.')]
class ProjectStatus extends SoftDeletableEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'Id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'ProjectStatusesLabel', type: 'string', length: 45, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 45)]
    private string $label;

    #[ORM\Column(name: 'ProjectStatusesCreated', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'ProjectStatusesUpdated', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(name: 'ProjectStatusesIsFinal', type: 'boolean', nullable: true)]
    private ?bool $final = false;

    public function __construct(string $label)
    {
        $this->label = trim($label);
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function rename(string $label): self
    {
        $this->label = trim($label);

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

    public function isFinal(): ?bool
    {
        return $this->final;
    }

    public function setFinal(?bool $final): self
    {
        $this->final = $final;

        return $this;
    }

    #[ORM\PrePersist]
    public function ensureCreatedAt(): void
    {
        $this->createdAt ??= new DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function touchUpdatedAt(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->label;
    }
}

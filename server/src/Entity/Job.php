<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\JobRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: JobRepository::class)]
#[ORM\Table(name: 'Jobs')]
#[ORM\HasLifecycleCallbacks]
class Job extends SoftDeletableEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'Id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'JobsLabel', type: 'string', length: 45, nullable: true)]
    #[Assert\Length(max: 45)]
    private ?string $label;

    #[ORM\Column(name: 'JobsCreated', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'JobsUpdated', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(?string $label = null)
    {
        $this->label = $label === null ? null : trim($label);
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getLabel(): ?string { return $this->label; }
    public function setLabel(?string $label): self { $this->label = $label === null ? null : trim($label); return $this; }
    public function __toString(): string { return $this->label ?? ''; }

    #[ORM\PreUpdate]
    public function touchUpdatedAt(): void { $this->updatedAt = new DateTimeImmutable(); }
}

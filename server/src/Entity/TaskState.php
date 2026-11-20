<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TaskStateRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskStateRepository::class)]
#[ORM\Table(name: 'States')]
#[ORM\HasLifecycleCallbacks]
class TaskState extends SoftDeletableEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'Id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'StatesLabel', type: 'string', length: 45, nullable: true)]
    #[Assert\Length(max: 45)]
    private ?string $label;

    #[ORM\Column(name: 'StatesCreated', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'StatesUpdated', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(name: 'StatesIsDefault', type: 'boolean')]
    private bool $default = false;

    public function __construct(?string $label = null, bool $default = false)
    {
        $this->label = $label === null ? null : trim($label);
        $this->default = $default;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getLabel(): ?string { return $this->label; }
    public function setLabel(?string $label): self { $this->label = $label === null ? null : trim($label); return $this; }
    public function isDefault(): bool { return $this->default; }
    public function setDefault(bool $default): self { $this->default = $default; return $this; }

    #[ORM\PreUpdate]
    public function touchUpdatedAt(): void { $this->updatedAt = new DateTimeImmutable(); }
}

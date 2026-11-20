<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EmployerRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmployerRepository::class)]
#[ORM\Table(name: 'Employers')]
#[ORM\HasLifecycleCallbacks]
class Employer extends SoftDeletableEntity
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;
    #[ORM\Column(name: 'EmployersName', length: 50, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $name;
    #[ORM\Column(name: 'EmployersCreated', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;
    #[ORM\Column(name: 'EmployersUpdated', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string $name) { $this->name = trim($name); $this->createdAt = new DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = trim($name); return $this; }
    public function __toString(): string { return $this->name; }
    #[ORM\PreUpdate]
    public function updateTimestamp(): void { $this->updatedAt = new DateTimeImmutable(); }
}

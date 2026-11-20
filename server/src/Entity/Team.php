<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TeamRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
#[ORM\Table(name: 'Teams')]
#[ORM\HasLifecycleCallbacks]
class Team extends SoftDeletableEntity
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;
    #[ORM\Column(name: 'TeamName', length: 50, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $name;
    #[ORM\ManyToOne(targetEntity: WorkLocation::class)]
    #[ORM\JoinColumn(name: 'TeamsLocation', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?WorkLocation $location = null;
    #[ORM\Column(name: 'TeamsCreated', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;
    #[ORM\Column(name: 'TeamsUpdated', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;
    public function __construct(string $name) { $this->name = trim($name); $this->createdAt = new DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = trim($name); return $this; }
    public function getLocation(): ?WorkLocation { return $this->location; }
    public function setLocation(?WorkLocation $location): self { $this->location = $location; return $this; }
    public function __toString(): string { return $this->name; }
    #[ORM\PreUpdate] public function updateTimestamp(): void { $this->updatedAt = new DateTimeImmutable(); }
}

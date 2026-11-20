<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\WorkLocationRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WorkLocationRepository::class)]
#[ORM\Table(name: 'Locations')]
#[ORM\HasLifecycleCallbacks]
class WorkLocation extends SoftDeletableEntity
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;
    #[ORM\Column(name: 'LocationsName', length: 25, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 25)]
    private string $name;
    #[ORM\Column(name: 'LocationsAddress', length: 255, nullable: true)]
    private ?string $address = null;
    #[ORM\Column(name: 'LocationsCreated', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;
    #[ORM\Column(name: 'LocationsUpdated', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;
    public function __construct(string $name) { $this->name = trim($name); $this->createdAt = new DateTimeImmutable(); }
    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = trim($name); return $this; }
    public function getAddress(): ?string { return $this->address; }
    public function setAddress(?string $address): self { $this->address = $address; return $this; }
    public function __toString(): string { return $this->name; }
    #[ORM\PreUpdate] public function updateTimestamp(): void { $this->updatedAt = new DateTimeImmutable(); }
}

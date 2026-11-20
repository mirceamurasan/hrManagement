<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ServiceProviderRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ServiceProviderRepository::class)]
#[ORM\Table(name: 'Providers')]
#[ORM\HasLifecycleCallbacks]
class ServiceProvider extends SoftDeletableEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'Id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'ProvidersName', type: 'string', length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $name;

    #[ORM\Column(name: 'ProvidersCreated', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'ProvidersUpdated', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(string $name)
    {
        $this->name = trim($name);
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = trim($name); return $this; }
    public function __toString(): string { return $this->name; }

    #[ORM\PreUpdate]
    public function touchUpdatedAt(): void { $this->updatedAt = new DateTimeImmutable(); }
}

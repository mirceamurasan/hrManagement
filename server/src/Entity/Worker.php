<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\WorkerRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WorkerRepository::class)]
#[ORM\Table(name: 'Workers')]
#[ORM\HasLifecycleCallbacks]
class Worker extends SoftDeletableEntity
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'WorkersName', length: 150)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 150)]
    private string $name;

    #[ORM\Column(name: 'WorkersEmail', length: 180, nullable: true, unique: true)]
    #[Assert\Email]
    private ?string $externalEmail = null;

    #[ORM\Column(name: 'WorkersInitials', length: 5, nullable: true)]
    #[Assert\Length(max: 5)]
    private ?string $initials = null;

    #[ORM\OneToOne(targetEntity: UserAccount::class)]
    #[ORM\JoinColumn(name: 'WorkersAccount', referencedColumnName: 'id', unique: true, nullable: true, onDelete: 'SET NULL')]
    private ?UserAccount $account = null;

    #[ORM\ManyToOne(targetEntity: Job::class)]
    #[ORM\JoinColumn(name: 'WorkersJob', referencedColumnName: 'Id', nullable: true, onDelete: 'SET NULL')]
    private ?Job $job = null;

    #[ORM\ManyToOne(targetEntity: Team::class)]
    #[ORM\JoinColumn(name: 'WorkersTeam', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Team $team = null;

    #[ORM\ManyToOne(targetEntity: WorkLocation::class)]
    #[ORM\JoinColumn(name: 'WorkersLocation', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?WorkLocation $location = null;

    #[ORM\Column(name: 'StartWorkingDate', type: 'date_immutable', nullable: true)]
    private ?DateTimeImmutable $startDate = null;

    #[ORM\Column(name: 'EndWorkingDate', type: 'date_immutable', nullable: true)]
    private ?DateTimeImmutable $endDate = null;

    #[ORM\Column(name: 'WorkersCreated', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    public function __construct(string $name)
    {
        $this->name = trim($name);
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = trim($name); return $this; }
    public function getExternalEmail(): ?string { return $this->externalEmail; }
    public function setExternalEmail(?string $email): self { $this->externalEmail = $email === null || trim($email) === '' ? null : mb_strtolower(trim($email)); return $this; }
    public function getInitials(): ?string { return $this->initials; }
    public function setInitials(?string $initials): self { $this->initials = $initials === null || trim($initials) === '' ? null : mb_strtoupper(trim($initials)); return $this; }
    public function getAccount(): ?UserAccount { return $this->account; }
    public function setAccount(?UserAccount $account): self { $this->account = $account; return $this; }
    public function getJob(): ?Job { return $this->job; }
    public function setJob(?Job $job): self { $this->job = $job; return $this; }
    public function getTeam(): ?Team { return $this->team; }
    public function setTeam(?Team $team): self { $this->team = $team; return $this; }
    public function getLocation(): ?WorkLocation { return $this->location; }
    public function setLocation(?WorkLocation $location): self { $this->location = $location; return $this; }
    public function getStartDate(): ?DateTimeImmutable { return $this->startDate; }
    public function setStartDate(?DateTimeImmutable $date): self { $this->startDate = $date; return $this; }
    public function getEndDate(): ?DateTimeImmutable { return $this->endDate; }
    public function setEndDate(?DateTimeImmutable $date): self { $this->endDate = $date; return $this; }
    public function __toString(): string { return $this->name; }
}

<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProjectTaskRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ProjectTaskRepository::class)]
#[ORM\Table(name: 'Tasks')]
#[ORM\HasLifecycleCallbacks]
#[Assert\Callback('validateDateRange')]
class ProjectTask extends SoftDeletableEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'Id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(name: 'ProjectsIdProjects', referencedColumnName: 'Id', nullable: false)]
    private Project $project;

    #[ORM\Column(name: 'TasksName', type: 'string', length: 45)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 45)]
    private string $name;

    #[ORM\Column(name: 'TasksBeginAt', type: 'date_immutable')]
    private DateTimeImmutable $startDate;

    #[ORM\Column(name: 'TasksEndAt', type: 'date_immutable')]
    private DateTimeImmutable $endDate;

    #[ORM\Column(name: 'TasksOrder', type: 'smallint')]
    private int $sortOrder = 1;

    #[ORM\ManyToOne(targetEntity: TaskState::class)]
    #[ORM\JoinColumn(name: 'StatesIdStates', referencedColumnName: 'Id', nullable: false)]
    private TaskState $state;

    #[ORM\Column(name: 'LotsIdLots', type: 'integer', nullable: true)]
    private ?int $lotId = null;

    #[ORM\Column(name: 'TasksCreatedBy', type: 'integer')]
    private int $createdById;

    #[ORM\Column(name: 'TasksCreated', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'TasksUpdated', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    /** @var Collection<int, TaskAllocation> */
    #[ORM\OneToMany(mappedBy: 'task', targetEntity: TaskAllocation::class, cascade: ['persist'])]
    private Collection $allocations;

    public function __construct(Project $project, TaskState $state, int $createdById, string $name, DateTimeImmutable $startDate, DateTimeImmutable $endDate)
    {
        $this->project = $project;
        $this->state = $state;
        $this->createdById = $createdById;
        $this->name = trim($name);
        $this->startDate = $startDate->setTime(0, 0);
        $this->endDate = $endDate->setTime(0, 0);
        $this->createdAt = new DateTimeImmutable();
        $this->allocations = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getProject(): Project { return $this->project; }
    public function setProject(Project $project): self { $this->project = $project; return $this; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = trim($name); return $this; }
    public function getStartDate(): DateTimeImmutable { return $this->startDate; }
    public function setStartDate(DateTimeImmutable $date): self { $this->startDate = $date->setTime(0, 0); return $this; }
    public function getEndDate(): DateTimeImmutable { return $this->endDate; }
    public function setEndDate(DateTimeImmutable $date): self { $this->endDate = $date->setTime(0, 0); return $this; }
    public function getSortOrder(): int { return $this->sortOrder; }
    public function setSortOrder(int $sortOrder): self { $this->sortOrder = $sortOrder; return $this; }
    public function getState(): TaskState { return $this->state; }
    public function setState(TaskState $state): self { $this->state = $state; return $this; }
    public function getLotId(): ?int { return $this->lotId; }
    public function setLotId(?int $lotId): self { $this->lotId = $lotId; return $this; }
    public function getCreatedById(): int { return $this->createdById; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?DateTimeImmutable { return $this->updatedAt; }

    /** @return Collection<int, TaskAllocation> */
    public function getAllocations(): Collection { return $this->allocations; }

    public function addAllocation(TaskAllocation $allocation): self
    {
        if (!$this->allocations->contains($allocation)) {
            $this->allocations->add($allocation);
            $allocation->setTask($this);
        }

        return $this;
    }

    public function archive(): self
    {
        $this->setTrashed(true);
        foreach ($this->allocations as $allocation) {
            $allocation->setTrashed(true);
        }

        return $this;
    }

    public function validateDateRange(ExecutionContextInterface $context): void
    {
        if (isset($this->startDate, $this->endDate) && $this->startDate > $this->endDate) {
            $context->buildViolation('The task start date must be on or before its end date.')
                ->atPath('startDate')
                ->addViolation();
        }
    }

    #[ORM\PreUpdate]
    public function touchUpdatedAt(): void { $this->updatedAt = new DateTimeImmutable(); }
}

<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TaskAllocationRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TaskAllocationRepository::class)]
#[ORM\Table(name: 'TasksLoads')]
#[ORM\HasLifecycleCallbacks]
class TaskAllocation extends SoftDeletableEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'Id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ProjectTask::class, inversedBy: 'allocations')]
    #[ORM\JoinColumn(name: 'TasksIdTasks', referencedColumnName: 'Id', nullable: false)]
    private ProjectTask $task;

    #[ORM\ManyToOne(targetEntity: Job::class)]
    #[ORM\JoinColumn(name: 'JobsIdJobs', referencedColumnName: 'Id', nullable: false)]
    private Job $job;

    #[ORM\ManyToOne(targetEntity: ServiceProvider::class)]
    #[ORM\JoinColumn(name: 'ProvidersIdProviders', referencedColumnName: 'Id', nullable: true)]
    private ?ServiceProvider $provider = null;

    #[ORM\Column(name: 'TasksLoadsWorkers', type: 'integer', nullable: true)]
    private ?int $workerId = null;

    #[ORM\Column(name: 'TasksLoadsVolume', type: 'smallint')]
    #[Assert\Positive]
    private int $volume;

    #[ORM\Column(name: 'TasksLoadsCreatedBy', type: 'integer')]
    private int $createdById;

    #[ORM\Column(name: 'TasksLoadsCreated', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'TasksLoadsUpdated', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(ProjectTask $task, Job $job, int $volume, int $createdById)
    {
        $this->task = $task;
        $this->job = $job;
        $this->volume = $volume;
        $this->createdById = $createdById;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getTask(): ProjectTask { return $this->task; }
    public function setTask(ProjectTask $task): self { $this->task = $task; return $this; }
    public function getJob(): Job { return $this->job; }
    public function setJob(Job $job): self { $this->job = $job; return $this; }
    public function getProvider(): ?ServiceProvider { return $this->provider; }
    public function setProvider(?ServiceProvider $provider): self { $this->provider = $provider; return $this; }
    public function getWorkerId(): ?int { return $this->workerId; }
    public function setWorkerId(?int $workerId): self { $this->workerId = $workerId; return $this; }
    public function getVolume(): int { return $this->volume; }
    public function setVolume(int $volume): self { $this->volume = $volume; return $this; }
    public function getCreatedById(): int { return $this->createdById; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): ?DateTimeImmutable { return $this->updatedAt; }

    #[ORM\PreUpdate]
    public function touchUpdatedAt(): void { $this->updatedAt = new DateTimeImmutable(); }
}

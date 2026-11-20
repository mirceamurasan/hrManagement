<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\EmploymentContractRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmploymentContractRepository::class)]
#[ORM\Table(name: 'Contracts')]
#[ORM\HasLifecycleCallbacks]
class EmploymentContract extends SoftDeletableEntity
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: Worker::class)]
    #[ORM\JoinColumn(name: 'idworker', referencedColumnName: 'id', nullable: false)]
    private Worker $worker;
    #[ORM\ManyToOne(targetEntity: ContractType::class)]
    #[ORM\JoinColumn(name: 'contract_type_id', referencedColumnName: 'id', nullable: false)]
    private ContractType $type;
    #[ORM\ManyToOne(targetEntity: Employer::class)]
    #[ORM\JoinColumn(name: 'employer_id', referencedColumnName: 'id', nullable: false)]
    private Employer $employer;
    #[ORM\ManyToOne(targetEntity: Job::class)]
    #[ORM\JoinColumn(name: 'idjob', referencedColumnName: 'Id', nullable: false)]
    private Job $job;
    #[ORM\Column(name: 'comment', type: 'text', nullable: true)]
    private ?string $comment = null;
    #[ORM\Column(name: 'ContractsCreated', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;
    #[ORM\Column(name: 'ContractsUpdated', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct(Worker $worker, ContractType $type, Employer $employer, Job $job)
    {
        $this->worker = $worker; $this->type = $type; $this->employer = $employer; $this->job = $job;
        $this->createdAt = new DateTimeImmutable();
    }
    public function getId(): ?int { return $this->id; }
    public function getWorker(): Worker { return $this->worker; }
    public function setWorker(Worker $worker): self { $this->worker = $worker; return $this; }
    public function getType(): ContractType { return $this->type; }
    public function setType(ContractType $type): self { $this->type = $type; return $this; }
    public function getEmployer(): Employer { return $this->employer; }
    public function setEmployer(Employer $employer): self { $this->employer = $employer; return $this; }
    public function getJob(): Job { return $this->job; }
    public function setJob(Job $job): self { $this->job = $job; return $this; }
    public function getComment(): ?string { return $this->comment; }
    public function setComment(?string $comment): self { $this->comment = $comment === null ? null : trim($comment); return $this; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    #[ORM\PreUpdate]
    public function updateTimestamp(): void { $this->updatedAt = new DateTimeImmutable(); }
}

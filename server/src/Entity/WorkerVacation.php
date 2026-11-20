<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\WorkerVacationRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: WorkerVacationRepository::class)]
#[ORM\Table(name: 'Vacations')]
#[ORM\HasLifecycleCallbacks]
#[Assert\Callback('validateDates')]
class WorkerVacation extends SoftDeletableEntity
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: ContractPeriod::class)]
    #[ORM\JoinColumn(name: 'VacationPeriods', referencedColumnName: 'id', nullable: false)]
    private ContractPeriod $period;
    #[ORM\Column(name: 'startdate', type: 'date_immutable')]
    private DateTimeImmutable $startDate;
    #[ORM\Column(name: 'enddate', type: 'date_immutable')]
    private DateTimeImmutable $endDate;
    #[ORM\Column(name: 'morning', type: 'boolean')]
    private bool $morning = true;
    #[ORM\Column(name: 'afternoon', type: 'boolean')]
    private bool $afternoon = true;
    #[ORM\Column(name: 'coef', type: 'decimal', precision: 2, scale: 1)]
    private string $coefficient = '1.0';
    #[ORM\Column(name: 'VacationsCreated', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    public function __construct(ContractPeriod $period, DateTimeImmutable $startDate, DateTimeImmutable $endDate)
    {
        $this->period = $period;
        $this->startDate = $startDate->setTime(0, 0);
        $this->endDate = $endDate->setTime(0, 0);
        $this->createdAt = new DateTimeImmutable();
    }
    public function getId(): ?int { return $this->id; }
    public function getPeriod(): ContractPeriod { return $this->period; }
    public function setPeriod(ContractPeriod $period): self { $this->period = $period; return $this; }
    public function getWorker(): Worker { return $this->period->getContract()->getWorker(); }
    public function getStartDate(): DateTimeImmutable { return $this->startDate; }
    public function setStartDate(DateTimeImmutable $date): self { $this->startDate = $date->setTime(0, 0); return $this; }
    public function getEndDate(): DateTimeImmutable { return $this->endDate; }
    public function setEndDate(DateTimeImmutable $date): self { $this->endDate = $date->setTime(0, 0); return $this; }
    public function isMorning(): bool { return $this->morning; }
    public function setMorning(bool $morning): self { $this->morning = $morning; $this->computeCoefficient(); return $this; }
    public function isAfternoon(): bool { return $this->afternoon; }
    public function setAfternoon(bool $afternoon): self { $this->afternoon = $afternoon; $this->computeCoefficient(); return $this; }
    public function getCoefficient(): float { return (float) $this->coefficient; }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function computeCoefficient(): void
    {
        $this->coefficient = number_format(($this->morning ? 0.5 : 0.0) + ($this->afternoon ? 0.5 : 0.0), 1, '.', '');
    }
    public function validateDates(\Symfony\Component\Validator\Context\ExecutionContextInterface $context): void
    {
        if ($this->endDate < $this->startDate) {
            $context->buildViolation('The vacation end date must not be before its start date.')->atPath('endDate')->addViolation();
        }
        if (!$this->morning && !$this->afternoon) {
            $context->buildViolation('Choose at least morning or afternoon.')->atPath('morning')->addViolation();
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ContractPeriodRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContractPeriodRepository::class)]
#[ORM\Table(name: 'Periods')]
#[ORM\HasLifecycleCallbacks]
#[Assert\Callback('validateDates')]
class ContractPeriod extends SoftDeletableEntity
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: EmploymentContract::class)]
    #[ORM\JoinColumn(name: 'idcontract', referencedColumnName: 'id', nullable: false)]
    private EmploymentContract $contract;
    #[ORM\Column(name: 'startdate', type: 'date_immutable')]
    private DateTimeImmutable $startDate;
    #[ORM\Column(name: 'enddate', type: 'date_immutable', nullable: true)]
    private ?DateTimeImmutable $endDate = null;
    #[ORM\Column(name: 'tjm', type: 'integer', nullable: true)]
    #[Assert\Positive]
    private ?int $dailyRate = null;
    #[ORM\Column(name: 'serviceProvider', type: 'boolean')]
    private bool $serviceProvider = false;
    #[ORM\Column(name: 'PeriodsCreated', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    public function __construct(EmploymentContract $contract, DateTimeImmutable $startDate)
    {
        $this->contract = $contract;
        $this->startDate = $startDate->setTime(0, 0);
        $this->createdAt = new DateTimeImmutable();
    }
    public function getId(): ?int { return $this->id; }
    public function getContract(): EmploymentContract { return $this->contract; }
    public function setContract(EmploymentContract $contract): self { $this->contract = $contract; return $this; }
    public function getStartDate(): DateTimeImmutable { return $this->startDate; }
    public function setStartDate(DateTimeImmutable $date): self { $this->startDate = $date->setTime(0, 0); return $this; }
    public function getEndDate(): ?DateTimeImmutable { return $this->endDate; }
    public function setEndDate(?DateTimeImmutable $date): self { $this->endDate = $date?->setTime(0, 0); return $this; }
    public function getDailyRate(): ?int { return $this->dailyRate; }
    public function setDailyRate(?int $rate): self { $this->dailyRate = $rate; return $this; }
    public function isServiceProvider(): bool { return $this->serviceProvider; }
    public function setServiceProvider(bool $serviceProvider): self { $this->serviceProvider = $serviceProvider; return $this; }
    public function validateDates(\Symfony\Component\Validator\Context\ExecutionContextInterface $context): void
    {
        if ($this->endDate !== null && $this->endDate < $this->startDate) {
            $context->buildViolation('The period end date must not be before its start date.')->atPath('endDate')->addViolation();
        }
    }
}

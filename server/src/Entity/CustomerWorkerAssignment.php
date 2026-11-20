<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CustomerWorkerAssignmentRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CustomerWorkerAssignmentRepository::class)]
#[ORM\Table(name: 'CustomerWorkerAssignments')]
#[ORM\UniqueConstraint(name: 'UNIQ_CUSTOMER_WORKER_ACTIVE', columns: ['customer_id', 'worker_id'])]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['customer', 'worker'], message: 'This worker is already assigned to this customer.')]
class CustomerWorkerAssignment extends SoftDeletableEntity
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: Customer::class)]
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'Id', nullable: false, onDelete: 'CASCADE')]
    private Customer $customer;
    #[ORM\ManyToOne(targetEntity: Worker::class)]
    #[ORM\JoinColumn(name: 'worker_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?Worker $worker = null;
    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    public function __construct(Customer $customer, ?Worker $worker = null)
    {
        $this->customer = $customer;
        $this->worker = $worker;
        $this->createdAt = new DateTimeImmutable();
    }
    public function getId(): ?int { return $this->id; }
    public function getCustomer(): Customer { return $this->customer; }
    public function getWorker(): ?Worker { return $this->worker; }
}

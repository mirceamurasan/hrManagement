<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\CustomerWorkerAssignment;
use App\Entity\Project;
use App\Entity\Worker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

final class CustomerWorkerAssignmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, CustomerWorkerAssignment::class); }
    /** @return list<CustomerWorkerAssignment> */
    public function findActiveForCustomer(Customer $customer): array
    {
        return $this->createQueryBuilder('assignment')->addSelect('worker')->join('assignment.worker', 'worker')
            ->andWhere('assignment.customer = :customer')->andWhere('assignment.isTrashed = false')
            ->setParameter('customer', $customer)->orderBy('worker.name', 'ASC')->getQuery()->getResult();
    }

    public function createActiveWorkersForProjectQueryBuilder(Project $project): QueryBuilder
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('worker')
            ->from(Worker::class, 'worker')
            ->join(CustomerWorkerAssignment::class, 'assignment', 'WITH', 'assignment.worker = worker')
            ->andWhere('assignment.customer = :customer')
            ->andWhere('assignment.isTrashed = false')
            ->andWhere('worker.isTrashed = false')
            ->setParameter('customer', $project->getCustomer())
            ->orderBy('worker.name', 'ASC');
    }
}

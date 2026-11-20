<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    /** @return list<Customer> */
    public function findActive(): array
    {
        return $this->createQueryBuilder('customer')
            ->andWhere('customer.isTrashed = false')
            ->orderBy('customer.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findBySapId(string $sapId): ?Customer
    {
        return $this->findOneBy(['sapId' => trim($sapId)]);
    }
}

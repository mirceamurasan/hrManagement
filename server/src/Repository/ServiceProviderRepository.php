<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ServiceProvider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ServiceProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, ServiceProvider::class); }

    /** @return list<ServiceProvider> */
    public function findActive(): array
    {
        return $this->createQueryBuilder('provider')
            ->andWhere('provider.isTrashed = false')
            ->orderBy('provider.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}

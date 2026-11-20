<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ImportRun;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ImportRunRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) { parent::__construct($registry, ImportRun::class); }
}

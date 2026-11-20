<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UserAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class UserAccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserAccount::class);
    }

    public function findByEmail(string $email): ?UserAccount
    {
        return $this->findOneBy(['email' => mb_strtolower(trim($email))]);
    }
}

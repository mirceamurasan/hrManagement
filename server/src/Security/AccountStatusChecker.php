<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\UserAccount;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class AccountStatusChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if ($user instanceof UserAccount && !$user->isActive()) {
            throw new CustomUserMessageAccountStatusException('This account is inactive.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
    }
}

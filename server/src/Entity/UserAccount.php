<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserAccountRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserAccountRepository::class)]
#[ORM\Table(name: 'app_users')]
#[UniqueEntity(fields: ['email'], message: 'An account with this email already exists.')]
class UserAccount implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private string $email;

    /** @var list<string> */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column]
    private string $password;

    #[ORM\Column]
    private bool $active = true;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    public function __construct(string $email)
    {
        $this->email = mb_strtolower(trim($email));
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = mb_strtolower(trim($email));

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /** @return list<string> */
    public function getRoles(): array
    {
        return array_values(array_unique(['ROLE_USER', ...$this->roles]));
    }

    /** @param list<string> $roles */
    public function setRoles(array $roles): self
    {
        $this->roles = array_values(array_unique($roles));

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $passwordHash): self
    {
        $this->password = $passwordHash;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function deactivate(): self
    {
        $this->active = false;

        return $this;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function activate(): self
    {
        $this->active = true;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function eraseCredentials(): void
    {
    }
}

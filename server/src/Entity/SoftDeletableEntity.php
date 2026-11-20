<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class SoftDeletableEntity
{
    #[ORM\Column(name: 'isTrashed', type: 'boolean', options: ['default' => false])]
    protected bool $isTrashed = false;

    public function isTrashed(): bool
    {
        return $this->isTrashed;
    }

    public function setTrashed(bool $isTrashed): static
    {
        $this->isTrashed = $isTrashed;

        return $this;
    }

    #[ORM\PrePersist]
    public function initializeTrashedState(): void
    {
        $this->isTrashed = false;
    }
}

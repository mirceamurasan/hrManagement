<?php

declare(strict_types=1);

namespace App\Import;

final readonly class WorkerCsvRow
{
    public function __construct(
        public int $lineNumber,
        public string $email,
        public string $firstName,
        public string $lastName,
        public string $initials,
    ) {
    }

    public function fullName(): string
    {
        return trim($this->firstName.' '.$this->lastName);
    }
}

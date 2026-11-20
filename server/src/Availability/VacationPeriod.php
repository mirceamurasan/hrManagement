<?php

declare(strict_types=1);

namespace App\Availability;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class VacationPeriod
{
    public function __construct(
        public DateTimeImmutable $startDate,
        public DateTimeImmutable $endDate,
        public float $coefficient,
        public bool $trashed = false,
    ) {
        if ($endDate < $startDate) {
            throw new InvalidArgumentException('The vacation end date must not be before its start date.');
        }

        if ($coefficient < 0) {
            throw new InvalidArgumentException('The vacation coefficient must not be negative.');
        }
    }
}

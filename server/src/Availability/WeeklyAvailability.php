<?php

declare(strict_types=1);

namespace App\Availability;

final readonly class WeeklyAvailability
{
    public function __construct(
        public string $week,
        public \DateTimeImmutable $startDate,
        public \DateTimeImmutable $endDate,
        public int $resourceCount,
        public int $businessDays,
        public float $daysOff,
        public float $availableDays,
    ) {
    }
}

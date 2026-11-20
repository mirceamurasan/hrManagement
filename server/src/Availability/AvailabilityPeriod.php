<?php

declare(strict_types=1);

namespace App\Availability;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class AvailabilityPeriod
{
    /**
     * @param list<VacationPeriod> $vacations
     */
    public function __construct(
        public int|string $id,
        public DateTimeImmutable $startDate,
        public ?DateTimeImmutable $endDate,
        public array $vacations = [],
    ) {
        if ($endDate !== null && $endDate < $startDate) {
            throw new InvalidArgumentException('The period end date must not be before its start date.');
        }

        foreach ($vacations as $vacation) {
            if (!$vacation instanceof VacationPeriod) {
                throw new InvalidArgumentException('Every vacation must be a VacationPeriod.');
            }
        }
    }
}

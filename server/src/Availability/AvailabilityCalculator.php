<?php

declare(strict_types=1);

namespace App\Availability;

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;

/**
 * Calculates weekly capacity from already selected contract periods.
 *
 * Keeping this service independent from persistence lets the legacy rules be
 * migrated and reviewed before a storage choice is made for the new app.
 */
final class AvailabilityCalculator
{
    /**
     * @param list<array{
     *     id: int|string,
     *     startDate: DateTimeInterface,
     *     endDate: DateTimeInterface|null,
     *     vacations?: list<array{
     *         startDate: DateTimeInterface,
     *         endDate: DateTimeInterface,
     *         coefficient: int|float|string,
     *         trashed?: bool
     *     }>
     * }> $periods Periods already filtered for the requested job and employee type.
     * @param list<DateTimeInterface|string> $holidays Legal holidays to exclude.
     */
    public function forEmployees(
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
        array $periods,
        array $holidays = [],
    ): WeeklyAvailability {
        [$start, $end, $holidayKeys] = $this->prepareRange($startDate, $endDate, $holidays);
        $businessDays = 0;
        $daysOff = 0.0;
        $availableDays = 0.0;
        $activePeriodIds = [];

        foreach ($this->daysBetween($start, $end) as $day) {
            $key = $day->format('Y-m-d');
            if ($this->isNonWorkingDay($day, $holidayKeys)) {
                continue;
            }

            ++$businessDays;
            $activePeriods = array_filter($periods, fn (array $period): bool =>
                $this->isActiveOn($period, $day)
            );
            $dayCapacity = count($activePeriods);
            $activePeriodIds = array_merge($activePeriodIds, array_column($activePeriods, 'id'));
            $dayOff = 0.0;

            foreach ($activePeriods as $period) {
                foreach ($period['vacations'] ?? [] as $vacation) {
                    if (($vacation['trashed'] ?? false) === false
                        && $this->contains($vacation['startDate'], $vacation['endDate'], $day)) {
                        $dayOff += (float) $vacation['coefficient'];
                    }
                }
            }

            $daysOff += $dayOff;
            $availableDays += $dayCapacity - $dayOff;
        }

        return new WeeklyAvailability(
            $start->format('W'),
            $start,
            $end,
            count(array_unique($activePeriodIds, SORT_REGULAR)),
            $businessDays,
            $daysOff,
            $availableDays,
        );
    }

    /**
     * Service-provider capacity follows the legacy rule: each active period
     * contributes one available day and does not subtract employee vacations.
     *
     * @param list<array{id: int|string, startDate: DateTimeInterface, endDate: DateTimeInterface|null}> $periods
     * @param list<DateTimeInterface|string> $holidays
     */
    public function forServiceProviders(
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
        array $periods,
        array $holidays = [],
    ): WeeklyAvailability {
        [$start, $end, $holidayKeys] = $this->prepareRange($startDate, $endDate, $holidays);
        $businessDays = 0;
        $availableDays = 0;
        $activePeriodIds = [];

        foreach ($this->daysBetween($start, $end) as $day) {
            if ($this->isNonWorkingDay($day, $holidayKeys)) {
                continue;
            }

            ++$businessDays;
            foreach ($periods as $period) {
                if ($this->isActiveOn($period, $day)) {
                    ++$availableDays;
                    $activePeriodIds[] = $period['id'];
                }
            }
        }

        return new WeeklyAvailability(
            $start->format('W'),
            $start,
            $end,
            count(array_unique($activePeriodIds, SORT_REGULAR)),
            $businessDays,
            0.0,
            (float) $availableDays,
        );
    }

    /** @param list<DateTimeInterface|string> $holidays
     *  @return array{DateTimeImmutable, DateTimeImmutable, array<string, true>}
     */
    private function prepareRange(
        DateTimeInterface $startDate,
        DateTimeInterface $endDate,
        array $holidays,
    ): array {
        $start = DateTimeImmutable::createFromInterface($startDate)->setTime(0, 0);
        $end = DateTimeImmutable::createFromInterface($endDate)->setTime(0, 0);
        if ($end < $start) {
            throw new InvalidArgumentException('The end date must not be before the start date.');
        }

        $holidayKeys = [];
        foreach ($holidays as $holiday) {
            $date = is_string($holiday) ? new DateTimeImmutable($holiday) : $holiday;
            $holidayKeys[DateTimeImmutable::createFromInterface($date)->format('Y-m-d')] = true;
        }

        return [$start, $end, $holidayKeys];
    }

    /** @return iterable<DateTimeImmutable> */
    private function daysBetween(DateTimeImmutable $start, DateTimeImmutable $end): iterable
    {
        for ($day = $start; $day <= $end; $day = $day->modify('+1 day')) {
            yield $day;
        }
    }

    /** @param array<string, true> $holidayKeys */
    private function isNonWorkingDay(DateTimeImmutable $day, array $holidayKeys): bool
    {
        return $day->format('N') >= 6 || isset($holidayKeys[$day->format('Y-m-d')]);
    }

    /** @param array{startDate: DateTimeInterface, endDate: DateTimeInterface|null} $period */
    private function isActiveOn(array $period, DateTimeImmutable $day): bool
    {
        return $this->contains($period['startDate'], $period['endDate'], $day);
    }

    private function contains(
        DateTimeInterface $startDate,
        ?DateTimeInterface $endDate,
        DateTimeImmutable $day,
    ): bool {
        $start = DateTimeImmutable::createFromInterface($startDate)->setTime(0, 0);
        $end = $endDate === null
            ? null
            : DateTimeImmutable::createFromInterface($endDate)->setTime(0, 0);

        return $start <= $day && ($end === null || $end >= $day);
    }
}

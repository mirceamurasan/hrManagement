<?php

declare(strict_types=1);

namespace App\Controller;

use App\Availability\AvailabilityCalculator;
use App\Availability\AvailabilityPeriod;
use App\Availability\VacationPeriod;
use App\Entity\Worker;
use App\Repository\TaskAllocationRepository;
use App\Repository\ContractPeriodRepository;
use App\Repository\JobRepository;
use App\Repository\PublicHolidayRepository;
use App\Repository\WorkerVacationRepository;
use App\Repository\WorkerRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reporting', name: 'reporting_')]
#[IsGranted('ROLE_PM')]
final class ReportingController extends AbstractController
{
    #[Route('/availability', name: 'availability', methods: ['GET'])]
    public function availability(
        Request $request,
        ContractPeriodRepository $periodRepository,
        WorkerVacationRepository $vacationRepository,
        PublicHolidayRepository $holidayRepository,
        JobRepository $jobRepository,
        AvailabilityCalculator $calculator,
    ): Response {
        $startDate = new DateTimeImmutable($request->query->get('start', 'monday this week'));
        $startDate = $startDate->modify('monday this week')->setTime(0, 0);
        $weeks = max(1, min(12, $request->query->getInt('weeks', 4)));
        $endDate = $startDate->modify(sprintf('+%d days', $weeks * 7 - 1));

        $periods = $periodRepository->findActive();
        $vacationsByPeriod = [];
        foreach ($vacationRepository->findActive() as $vacation) {
            $vacationsByPeriod[$vacation->getPeriod()->getId()][] = new VacationPeriod(
                $vacation->getStartDate(),
                $vacation->getEndDate(),
                $vacation->getCoefficient(),
            );
        }

        $holidays = array_map(
            static fn ($holiday): DateTimeImmutable => $holiday->getDate(),
            $holidayRepository->findBetween($startDate, $endDate),
        );
        $rows = [];

        foreach ($jobRepository->findActive() as $job) {
            $jobPeriods = array_values(array_filter(
                $periods,
                static fn ($period): bool => $period->getContract()->getJob()->getId() === $job->getId(),
            ));

            for ($week = 0; $week < $weeks; ++$week) {
                $weekStart = $startDate->modify(sprintf('+%d weeks', $week));
                $weekEnd = $weekStart->modify('+6 days');
                $employeePeriods = [];
                $providerPeriods = [];

                foreach ($jobPeriods as $period) {
                    $availabilityPeriod = new AvailabilityPeriod(
                        $period->getId() ?? 0,
                        $period->getStartDate(),
                        $period->getEndDate(),
                        $vacationsByPeriod[$period->getId()] ?? [],
                    );
                    if ($period->isServiceProvider()) {
                        $providerPeriods[] = $availabilityPeriod;
                    } else {
                        $employeePeriods[] = $availabilityPeriod;
                    }
                }

                $rows[] = [
                    'job' => $job,
                    'week' => $weekStart,
                    'employees' => $calculator->forEmployees($weekStart, $weekEnd, $employeePeriods, $holidays),
                    'providers' => $calculator->forServiceProviders($weekStart, $weekEnd, $providerPeriods, $holidays),
                ];
            }
        }

        return $this->render('reporting/availability.html.twig', [
            'rows' => $rows,
            'startDate' => $startDate,
            'weeks' => $weeks,
        ]);
    }

    #[Route('/weekly-review', name: 'weekly_review', methods: ['GET'])]
    public function weeklyReview(
        Request $request,
        WorkerRepository $workers,
        TaskAllocationRepository $allocations,
        WorkerVacationRepository $vacations,
    ): Response {
        $rawStart = (string) $request->query->get('start', (new DateTimeImmutable('monday this week'))->format('Y-m-d'));
        $selectedDate = DateTimeImmutable::createFromFormat('!Y-m-d', $rawStart);
        $dateErrors = DateTimeImmutable::getLastErrors();
        if ($selectedDate === false || ($dateErrors !== false && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0))) {
            $selectedDate = new DateTimeImmutable('monday this week');
        }
        $startDate = $selectedDate->modify('monday this week')->setTime(0, 0);
        $endDate = $startDate->modify('+6 days');
        $workerId = $request->query->getInt('worker') ?: null;
        $worker = $workerId === null ? null : $workers->find($workerId);
        if ($workerId !== null && (!$worker instanceof Worker || $worker->isTrashed())) {
            throw $this->createNotFoundException('Worker not found.');
        }

        return $this->render('reporting/weekly_review.html.twig', [
            'workers' => $workers->findActive(),
            'selectedWorker' => $worker,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'allocations' => $allocations->findForWeek($startDate, $endDate, $worker),
            'vacations' => $vacations->findOverlapping($startDate, $endDate, $worker),
        ]);
    }
}

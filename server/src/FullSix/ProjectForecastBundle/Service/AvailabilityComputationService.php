<?php

namespace FullSix\ProjectForecastBundle\Service;

use FullSix\ProjectForecastBundle\Entity\Periods;
use FullSix\ProjectForecastBundle\Entity\Vacations;

class AvailabilityComputationService {

    protected $doctrine;

    public function __construct($doctrine) {
        $this->doctrine = $doctrine;
    }

    /**
     * Computes the weekly availability for a team member
     *
     * @param int $jobsid
     * @param string $startDate
     * @param string $endDate
     *
     * @return array
     */
    public function getTeamMemberWeeklyAvailabilityByJobId($jobsid, $startDate, $endDate) {

        $dayCount = 0;
        $tmAvailable = 0;
        $tmUnavailable = 0;
        $tmTotal = 0;

        $employeesArray = array();

        $week = $startDate->format('W');

        $periodsRepo = $this->getDoctrine()->getManager()->getRepository('FullSixProjectForecastBundle:Periods');
        $vacationsRepo = $this->getDoctrine()->getManager()->getRepository('FullSixProjectForecastBundle:Vacations');

        $period = $this->getPeriodBetweenDates($startDate, $endDate);
        foreach ($period as $day)
        {
            // exclude Saturdays, Sundays and legal holidays
            if (!(in_array($day->format('w'), array(0, 6)) || $this->isLegalHoliday($day)))
            {
                $dayCount++;
                // Check the periods in the DB containing the certain day
                $periodObjects = $periodsRepo->findActivePeriodsContainingDateByJobAndSP($day->format('Y-m-d'), $jobsid, false);
                /** @var Periods $periodEntry */

                foreach ($periodObjects as $periodEntry)
                {
                    if (!in_array($periodEntry, $employeesArray))
                    {
                        $employeesArray[] = $periodEntry;
                    }
                    $tmTotal++;

                    // add up all the vacation days
                    $vacationObjects = $periodEntry->getPeriodvacations();
                    /** @var Vacations $vacationEntry */
                    foreach($vacationObjects as $vacationEntry)
                    {
                        if(!$vacationEntry->getIsTrashed() &&
                            $vacationEntry->containsDay($day)){

                            $tmUnavailable += $vacationEntry->getCoef();
                        }
                    }
                }


            }
            // Compute the result
            $tmAvailable = $tmTotal - $tmUnavailable;
        }
        return array(
            'week' => $week,
            'beginDate' => $startDate,
            'endDate' => $endDate,
            'employes' => count($employeesArray),
            'daysOpen' => $dayCount,
            'NbDaysOff' => $tmUnavailable,
            'DaysDispo' => $tmAvailable
        );

    }

    /**
     * Computes the weekly availability for a service provider
     *
     * @param int $jobsid
     * @param string $startDate
     * @param string $endDate
     *
     * @return array
     */
    public function getServiceProviderWeeklyAvailabilityByJobId($jobsid, $startDate, $endDate) {

        $dayCount = 0;
        $spAvailable = 0;
        $prestatairesArray = array();
        $week = $startDate->format('W');

        $periodsRepo = $this->getDoctrine()->getManager()->getRepository('FullSixProjectForecastBundle:Periods');

        $period = $this->getPeriodBetweenDates($startDate, $endDate);

        foreach ($period as $day)
        {
            // exclude Saturdays, Sundays and legal holidays
            if (!(in_array($day->format('w'), array(0, 6)) || $this->isLegalHoliday($day)))
            {
                $dayCount++;
                $periodObjects = $periodsRepo->findActivePeriodsContainingDateByJobAndSP($day->format('Y-m-d'), $jobsid, true);

                foreach ($periodObjects as $periodEntry)
                {
                    if (!in_array($periodEntry, $prestatairesArray))
                    {
                        $prestatairesArray[] = $periodEntry;
                    }
                    $spAvailable++;
                }

            }
        }

        return array(
            'week' => $week,
            'beginDate' => $startDate,
            'endDate' => $endDate,
            'prestataires' => count($prestatairesArray),
            'daysOpen' => $dayCount,
            'NbDaysOff' => 0,
            'DaysDispo' => $spAvailable
        );
    }

    /**
     * Checks if a date is a legal holiday
     *
     * @param string $date
     *
     * @return mixed
     */
    protected function isLegalHoliday($date) {
        $daysOffRepo = $this->getDoctrine()->getManager()->getRepository('FullSixProjectForecastBundle:DaysoffsGlobal');
        $isDayOff = $daysOffRepo->findBy(array('DaysoffsGlobalDate' => $date));
        return $isDayOff;
    }

    /**
     * Return the period between two dates
     *
     * @param string $startDate
     * @param string $endDate
     *
     * @return \DatePeriod
     */
    protected function getPeriodBetweenDates($startDate, $endDate) {
        // Add one day in order to get all the days of the week
        $endDate->modify('+1 day');

        $interval = \DateInterval::createFromDateString('1 day');
        $period = new \DatePeriod($startDate, $interval, $endDate);

        $endDate->modify('-1 day');

        return $period;
    }

    /**
     * Returns the Doctrine instance
     *
     * @return mixed
     */
    public function getDoctrine() {
        return $this->doctrine;
    }
}
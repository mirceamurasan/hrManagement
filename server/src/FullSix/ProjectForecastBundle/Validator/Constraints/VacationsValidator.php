<?php
namespace FullSix\ProjectForecastBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class VacationsValidator extends ConstraintValidator
{
    public function validate($vacation, Constraint $constraint)
    {
        if ($vacation->getStartdate() > $vacation->getEnddate()) {
            $this->context->addViolation("worker.vacation.cannotCreate.invalidRange");
        }

        $worker = $vacation->getWorker();
        if ($worker) {
            $period = $worker->getPeriodContainingVacation($vacation);
            if (!$period) {
                $this->context->addViolation("worker.vacation.cannotCreate.noPeriod");
            } elseif ($period->getIsTrashed()) {
                $this->context->addViolation("worker.vacation.cannotCreate.inactivePeriod");
            } elseif ($this->testIfThereAreAlreadyVacations($period, $vacation)) {
                $this->context->addViolation("worker.vacation.cannotCreate.alreadyVacation");
            } else {
                $vacation->setVacationperiods($period);
            }
        }else{
            //should never arrive
            $this->context->addViolation("Not linked to a worker");
        }
    }

    private function testIfThereAreAlreadyVacations($period, $vacation)
    {
        foreach ($period->getPeriodvacations() as $periodVacation) {
            if ($periodVacation != $vacation && $this->overlapVacations($vacation, $periodVacation)) {
                return true;
            }
        }

        return false;
    }

    private function overlapVacations($vacation1, $vacation2)
    {
        if ($vacation1->getStartdate() <= $vacation2->getStartdate() &&
            $vacation2->getStartdate() <= $vacation1->getEnddate()
        ) {
            return true;
        }
        if ($vacation2->getStartdate() <= $vacation1->getStartdate() &&
            $vacation1->getStartdate() <= $vacation2->getEnddate()
        ) {
            return true;
        }

        return false;
    }

}
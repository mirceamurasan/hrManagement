<?php
namespace FullSix\ProjectForecastBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PeriodDateValidator extends ConstraintValidator
{
    public function validate($period, Constraint $constraint)
    {
        if($period->getEnddate()){
            if ($period->getStartdate() > $period->getEnddate()) {
                $this->context->addViolation(
                    "worker.contract.period.cannotCreate.invalidRange"
                );
            }
        }

        $contract = $period->getIdcontract();
        if ($contract) {
            if ($contract->getIsTrashed()) {
                $this->context->addViolation("worker.contract.period.cannotCreate.inactiveContract");
            }

            $worker = $contract->getIdworker();
            foreach ($worker->getContracts() as $workerContract) {
                foreach ($workerContract->getPeriods() as $workerPeriod) {
                    if ($workerPeriod != $period && $this->overlapPeriods($period, $workerPeriod)) {
                        $this->context->addViolation("worker.contract.period.cannotCreate.overlapingPeriod");
                        break 2; //break both foreach loops
                    }
                }
            }
        }

        $vacations = $period->getPeriodvacations();
        if($vacations){
            foreach ($vacations as $vacation) {
                if (!$this->containedVacationByPeriod($vacation, $period)) {
                    $this->context->addViolation("worker.contract.period.cannotCreate.outVacation");
                    break ; //break both foreach loops
                }
            }
        }

    }

    private function overlapPeriods($period1, $period2)
    {
        //if end date = null => infinit
        if ($period1->getStartdate() <= $period2->getStartdate() &&
            (!$period1->getEnddate() || $period2->getStartdate() <= $period1->getEnddate())) {
            return true;
        }
        if ($period2->getStartdate() <= $period1->getStartdate() &&
            (!$period2->getEnddate() ||  $period1->getStartdate() <= $period2->getEnddate())
        ) {
            return true;
        }

        return false;
    }

    private function containedVacationByPeriod($vacation, $period)
    {
        //if end date = null => infinit
        if ($period->getStartdate() <= $vacation->getStartdate() &&
            (!$period->getEnddate() || $vacation->getEnddate() <= $period->getEnddate())) {
            return true;
        }

        return false;
    }
}
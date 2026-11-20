<?php

namespace FullSix\ProjectForecastBundle\Helper;

class DenormalisationHelper
{

    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    /*
     * update Worker information + service provider value of it's periods
     * */
    public function crudContractDenormalisation($contract)
    {
        foreach ($contract->getPeriods() as $period) {
            $this->crudPeriodDenormalisation($period);
        }
    }

    /*
     * update Worker information
     * */
    public function crudPeriodDenormalisation($period)
    {
        $sp = $period->getIdcontract()->getEmployer()->getAgenciesidagencies() ? false : true;
        $today = new \DateTime();

        $period->setServiceprovider($sp);


        if ($period->getStartdate() <= $today && $period->getEnddate() >= $today) {
            $worker = $period->getIdcontract()->getIdworker();
            $worker->setStartworkingdate($period->getStartdate());
            $worker->setEndworkingdate($period->getEnddate());
            $worker->setContracttype($period->getIdContract()->getContracttype());
            $worker->setEmployer($period->getIdContract()->getEmployer());
            $worker->setJob($period->getIdContract()->getIdjob());
            $this->em->persist($worker);
        }
        $this->em->persist($period);
        $this->em->flush();
    }

    /*
     * update service provider value of it's contract periods
     * */
    public function updateEmployerDenormalisation($employer)
    {
        $sp = $employer->getAgenciesidagencies() ? false : true;
        foreach ($employer->getContracts() as $contract) {
            foreach ($contract->getPeriods() as $period) {
                $period->setServiceprovider($sp);
                $this->em->persist($period);
            }
        }
        $this->em->flush();
    }

}

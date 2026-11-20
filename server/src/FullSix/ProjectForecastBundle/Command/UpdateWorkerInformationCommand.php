<?php

namespace FullSix\ProjectForecastBundle\Command;

use FullSix\ProjectForecastBundle\Entity\Contracts;
use FullSix\ProjectForecastBundle\Entity\Periods;
use Proxies\__CG__\FullSix\ProjectForecastBundle\Entity\Workers;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use FullSix\ProjectForecastBundle\Repository\PeriodsRepository;

class UpdateWorkerInformationCommand extends ContainerAwareCommand
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('utils:updateWorkerInformation')
            ->setDescription("Updates workers' information about employer, job and contract type")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        /** @var PeriodsRepository $periodsRepo */
        $periodsRepo = $em->getRepository('FullSixProjectForecastBundle:Periods');
        $periodObjects = $periodsRepo->findPeriodsStartingOn(date('Y-m-d'));

        if (count($periodObjects) > 0)
        {
            /** @var Periods $period */
            foreach ($periodObjects as $period)
            {
                $contract = $period->getIdcontract();
                if ($contract instanceof Contracts)
                {
                    $worker = $contract->getIdworker();
                    $worker->setStartworkingdate($period->getStartdate());
                    $worker->setEndworkingdate($period->getEnddate());
                    $worker->setJob($contract->getIdjob());
                    $worker->setEmployer($contract->getEmployer());
                    $worker->setContracttype($contract->getContracttype());
                    $em->persist($worker);
                }
            }

            $em->flush();
        }

    }
}

<?php

namespace FullSix\ProjectForecastBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProjectImportCommand extends ContainerAwareCommand
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('utils:importProjects')
            ->setDescription('Import projects from CSV files to application.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $csvImportService = $this->getContainer()->get('fullsix_projectforecast.csv_import');
        $csvImportService->import();
    }
}

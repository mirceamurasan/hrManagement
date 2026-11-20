<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Customer;
use App\Entity\ImportRun;
use App\Entity\Project;
use App\Entity\ProjectStatus;
use App\Import\ProjectCsvParser;
use App\Import\ProjectCsvRow;
use App\Repository\CustomerRepository;
use App\Repository\ProjectRepository;
use App\Repository\ProjectStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:import:projects', description: 'Import projects from the legacy SAP CSV export.')]
final class ImportProjectsCommand extends Command
{
    public function __construct(
        private readonly ProjectCsvParser $parser,
        private readonly CustomerRepository $customers,
        private readonly ProjectRepository $projects,
        private readonly ProjectStatusRepository $statuses,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to the semicolon-delimited SAP CSV file')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Parse and validate without changing the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $path = (string) $input->getArgument('file');
        $dryRun = (bool) $input->getOption('dry-run');
        $created = 0;
        $updated = 0;
        $errors = [];
        $source = basename($path);

        try {
            foreach ($this->parser->parseFile($path) as $row) {
                if (!$row->hasRequiredFields() || mb_strlen($row->projectName) > 250 || mb_strlen($row->customerName) > 50 || mb_strlen($row->status) > 45) {
                    $errors[] = ['line' => $row->lineNumber, 'message' => $this->rowError($row)];
                    continue;
                }

                $customer = $this->customers->findBySapId($row->customerSapId)
                    ?? $this->customers->findOneBy(['name' => $row->customerName]);
                if ($customer === null && $row->customerName === '') {
                    $errors[] = ['line' => $row->lineNumber, 'message' => 'Customer name is required when the SAP customer ID is new.'];
                    continue;
                }
                if ($customer === null) {
                    $customer = new Customer($row->customerName, 0);
                    $customer->setSapId($row->customerSapId);
                    if (!$dryRun) {
                        $this->entityManager->persist($customer);
                    }
                } elseif ($customer->getSapId() !== null && $customer->getSapId() !== $row->customerSapId) {
                    $errors[] = ['line' => $row->lineNumber, 'message' => 'Customer name is already linked to another SAP ID.'];
                    continue;
                } elseif ($customer->getSapId() === null) {
                    $customer->setSapId($row->customerSapId);
                    $customer->setName($row->customerName);
                }

                $status = $row->status === '' ? null : $this->statuses->findByLabel($row->status);
                if ($row->status !== '' && $status === null) {
                    $status = new ProjectStatus($row->status);
                    if (!$dryRun) {
                        $this->entityManager->persist($status);
                    }
                } elseif ($status !== null) {
                    $status->setTrashed(false);
                }

                $project = $this->projects->findBySapNumber($row->sapNumber);
                if ($project === null) {
                    $project = new Project($customer, 0, $row->projectName);
                    $project->setSapNumber($row->sapNumber);
                    ++$created;
                    if (!$dryRun) {
                        $this->entityManager->persist($project);
                    }
                } else {
                    $project->setTrashed(false)
                        ->setCustomer($customer)
                        ->setName($row->projectName);
                    ++$updated;
                }
                if ($status !== null) {
                    $project->setStatus($status);
                }
                if (!$dryRun) {
                    $this->entityManager->flush();
                }
            }
        } catch (RuntimeException $exception) {
            $errors[] = ['line' => 0, 'message' => $exception->getMessage()];
        }

        $success = $errors === [];
        if (!$dryRun) {
            $run = new ImportRun('projects', $source, $success, $created, $updated, $errors);
            $this->entityManager->persist($run);
            $this->entityManager->flush();
        }

        $io->table(['Result', 'Count'], [
            ['Projects created', $created],
            ['Projects updated', $updated],
            ['Errors', count($errors)],
            ['Mode', $dryRun ? 'Dry run' : 'Imported'],
        ]);
        foreach (array_slice($errors, 0, 20) as $error) {
            $io->warning(sprintf('Line %d: %s', $error['line'], $error['message']));
        }

        return $success ? Command::SUCCESS : Command::FAILURE;
    }

    private function rowError(ProjectCsvRow $row): string
    {
        if (!$row->hasRequiredFields()) {
            return 'Required SAP project number, project name, or customer SAP ID is missing.';
        }
        if (mb_strlen($row->projectName) > 250) {
            return 'Project name exceeds 250 characters.';
        }
        if (mb_strlen($row->customerName) > 50) {
            return 'Customer name exceeds 50 characters.';
        }

        return 'Project status exceeds 45 characters.';
    }
}

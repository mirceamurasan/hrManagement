<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\ImportRun;
use App\Entity\Worker;
use App\Import\WorkerCsvParser;
use App\Repository\UserAccountRepository;
use App\Repository\WorkerRepository;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:import:workers', description: 'Import worker profiles from a semicolon-separated CSV file.')]
final class ImportWorkersCommand extends Command
{
    public function __construct(
        private readonly WorkerCsvParser $parser,
        private readonly WorkerRepository $workers,
        private readonly UserAccountRepository $accounts,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'CSV columns: email;first name;last name;initials')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Parse and validate without changing the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $dryRun = (bool) $input->getOption('dry-run');
        $created = 0;
        $updated = 0;
        $errors = [];

        try {
            foreach ($this->parser->parseFile((string) $input->getArgument('file')) as $row) {
                if (filter_var($row->email, FILTER_VALIDATE_EMAIL) === false || $row->fullName() === '' || mb_strlen($row->fullName()) > 150) {
                    $errors[] = ['line' => $row->lineNumber, 'message' => 'A valid email and a name of 1–150 characters are required.'];
                    continue;
                }

                $account = $this->accounts->findByEmail($row->email);
                $worker = $this->workers->findOneBy(['externalEmail' => $row->email]);
                if ($worker === null && $account !== null) {
                    $worker = $this->workers->findOneBy(['account' => $account]);
                }
                if ($worker !== null && $account !== null && $worker->getAccount()?->getId() !== $account->getId()) {
                    $errors[] = ['line' => $row->lineNumber, 'message' => 'This worker is linked to a different sign-in account.'];
                    continue;
                }

                if ($worker === null) {
                    $worker = new Worker($row->fullName());
                    $worker->setExternalEmail($row->email)->setInitials($row->initials);
                    if ($account !== null) {
                        $worker->setAccount($account);
                    }
                    if (!$dryRun) {
                        $this->entityManager->persist($worker);
                    }
                    ++$created;
                } else {
                    $worker->setName($row->fullName())
                        ->setInitials($row->initials)
                        ->setExternalEmail($row->email);
                    if ($account !== null) {
                        $worker->setAccount($account);
                    }
                    ++$updated;
                }

                if (!$dryRun) {
                    $this->entityManager->flush();
                }
            }
        } catch (RuntimeException $exception) {
            $errors[] = ['line' => 0, 'message' => $exception->getMessage()];
        }

        if (!$dryRun) {
            $this->entityManager->persist(new ImportRun('workers', basename((string) $input->getArgument('file')), $errors === [], $created, $updated, $errors));
            $this->entityManager->flush();
        }

        $io->table(['Result', 'Count'], [
            ['Profiles created', $created],
            ['Profiles updated', $updated],
            ['Errors', count($errors)],
            ['Mode', $dryRun ? 'Dry run' : 'Imported'],
        ]);
        foreach (array_slice($errors, 0, 20) as $error) {
            $io->warning(sprintf('Line %d: %s', $error['line'], $error['message']));
        }

        return $errors === [] ? Command::SUCCESS : Command::FAILURE;
    }
}

<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\UserAccount;
use App\Repository\UserAccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:user:create', description: 'Create an application account from the command line.')]
final class CreateUserCommand extends Command
{
    public function __construct(
        private readonly UserAccountRepository $users,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED, 'Account email address');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = mb_strtolower(trim((string) $input->getArgument('email')));

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $io->error('Enter a valid email address.');

            return Command::INVALID;
        }

        if ($this->users->findByEmail($email) !== null) {
            $io->error('An account with this email already exists.');

            return Command::FAILURE;
        }

        $question = new Question('Password (input hidden): ');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $password = (string) $io->askQuestion($question);

        if (strlen($password) < 12) {
            $io->error('Use a password with at least 12 characters.');

            return Command::INVALID;
        }

        $account = new UserAccount($email);
        $account->setRoles(['ROLE_ADMIN']);
        $account->setPassword($this->passwordHasher->hashPassword($account, $password));

        $this->entityManager->persist($account);
        $this->entityManager->flush();

        $io->success(sprintf('Administrator account created for %s.', $email));

        return Command::SUCCESS;
    }
}

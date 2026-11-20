<?php

namespace FullSix\ProjectForecastBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface;

use FOS\UserBundle\Model\UserInterface;
use FullSix\ProjectForecastBundle\Entity\UsersImportReports;
use FullSix\ProjectForecastBundle\Entity\UsersImportErrors;

class LdapImportCommand extends ContainerAwareCommand
{
    protected $em;

    protected function configure()
    {
        $this
            ->setName('ldap:import')
            ->setDescription('Import LDAP users to application.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->getContainer()->getParameter('ldap_import.enabled')) {
            $userProvider = $this->getContainer()->get('imag_ldap.security.user.provider');
            $loginManager = $this->getContainer()->get('fos_user.user_manager');
            $em = $this->getContainer()->get('doctrine.orm.entity_manager');
            $ldapManager = $this->getContainer()->get('imag_ldap.ldap_manager');
            $report = new UsersImportReports();
            $countUsersUpdated = 0;
            $countUsersCreated = 0;
            $countUsersMarkedDirty = 0;
            $countErrors = 0;
            $failReason = '';
            $success = true;

            foreach ($loginManager->findUsers() as $login) {
                if ($login instanceof UserInterface && $login->getUser()->isAdLinked()) {
                    $response = $userProvider->updateUserFromLdapByUsername($login->getUsername());
                    $countUsersUpdated += $response['updated'];
                    if ($response['missing']) {
                        $countErrors++;
                        $error = new UsersImportErrors();
                        $error
                                ->setUsername($login->getUsername())
                                ->setEmail($login->getEmail())
                                ->setFirstname($login->getUser()->getUsersfirstname())
                                ->setLastname($login->getUser()->getUserslastname())
                                ->setInitials($login->getUser()->getUsersinitials())
                                ->setStatus('missing in AD')
                            ;
                        $report->addErrors($error);
                    }
                }
            }

            $allLdapUsers = $userProvider->anonymousSearchAllUsers("*");
            array_shift($allLdapUsers);
            foreach ($allLdapUsers as $ldapUser) {
                $username = $ldapManager->setLdapUser($ldapUser)->getUsername();
                if ($username) {
                    $response = $userProvider->createUsersLoginByUsername($username);
                    $countUsersCreated += $response['created'];
                    $countUsersMarkedDirty += $response['dirty'];
                } else {
                    $countErrors++;
                    $error = new UsersImportErrors();
                    $attributes = $ldapManager->getAttributes();
                    $error
                            ->setUsername($username)
                            ->setEmail($ldapManager->getEmail())
                            ->setFirstname($ldapManager->getGivenName())
                            ->setLastname($ldapManager->getSurname())
                            ->setInitials(isset($attributes['initials']) ? $attributes['initials'] : '')
                            ->setStatus('missing username')
                        ;
                    $report->addErrors($error);
                }
            }

            $exportDir = $this->getContainer()->getParameter('knp_snappy.path_reports.import_users');
            $reportName = '/users_import_report_' . $report->getImportDate()->format('YmdHis') . '.pdf';
            $exportFilename = rtrim($exportDir, "/") . $reportName;

            if (!$allLdapUsers) {
                $success = false;
                $failReason = 'Cannot access AD.';
            }

            $report->setPdf($reportName)
                    ->setSuccess($success)
                    ->setFailReason($failReason)
                    ->setCountUsersCreated($countUsersCreated)
                    ->setCountUsersUpdated($countUsersUpdated)
                    ->setCountUsersMarkedDirty($countUsersMarkedDirty)
                    ->setCountErrors($countErrors)
                ;

            $this->getContainer()->get('knp_snappy.pdf')->generateFromHtml(
                $this->getContainer()->get('templating')->render(
                    'FullSixProjectForecastBundle::usersImportReport.html.twig',
                    array(
                        'entity'  => $report
                    )
                ),
                $exportFilename
            );
            $em->persist($report);
            $em->flush();

            $text = 'Import fait : %d ';
            if ($countUsersUpdated !== 1) {
                $text .= 'utilisateurs mis a jour ; ';
            } else {
                $text .= 'utilisateur mis a jour ; ';
            }
            if ($countUsersCreated !== 1) {
                $text .= '%d utilisateurs crees.';
            } else {
                $text .= '%d utilisateur cree.';
            }
            $output->writeln(sprintf($text . " Errors: %d.", $countUsersUpdated, $countUsersCreated, $countErrors));
        } else {
            $output->writeln('Import disabled.');
        }
    }
}

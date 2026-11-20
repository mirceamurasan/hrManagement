<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Job;
use App\Entity\UserAccount;
use App\Entity\Worker;
use App\Entity\Team;
use App\Entity\WorkLocation;
use App\Repository\JobRepository;
use App\Repository\TeamRepository;
use App\Repository\WorkLocationRepository;
use App\Repository\UserAccountRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class WorkerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Full name'])
            ->add('initials', TextType::class, ['required' => false])
            ->add('externalEmail', EmailType::class, ['required' => false, 'label' => 'Work email'])
            ->add('account', EntityType::class, [
                'class' => UserAccount::class,
                'choice_label' => 'email',
                'required' => false,
                'placeholder' => 'No sign-in account',
                'query_builder' => static fn (UserAccountRepository $accounts) => $accounts->createQueryBuilder('account')->orderBy('account.email'),
            ])
            ->add('job', EntityType::class, [
                'class' => Job::class,
                'choice_label' => static fn (Job $job): string => $job->getLabel() ?? 'Unnamed job',
                'required' => false,
                'placeholder' => 'No job',
                'query_builder' => static fn (JobRepository $jobs) => $jobs->createQueryBuilder('job')->andWhere('job.isTrashed = false')->orderBy('job.label'),
            ])
            ->add('team', EntityType::class, [
                'class' => Team::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'No team',
                'query_builder' => static fn (TeamRepository $teams) => $teams->createQueryBuilder('team')->andWhere('team.isTrashed = false')->orderBy('team.name'),
            ])
            ->add('location', EntityType::class, [
                'class' => WorkLocation::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'No location',
                'query_builder' => static fn (WorkLocationRepository $locations) => $locations->createQueryBuilder('location')->andWhere('location.isTrashed = false')->orderBy('location.name'),
            ])
            ->add('startDate', DateType::class, ['label' => 'Start date', 'widget' => 'single_text', 'input' => 'datetime_immutable', 'required' => false])
            ->add('endDate', DateType::class, ['label' => 'End date', 'widget' => 'single_text', 'input' => 'datetime_immutable', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Worker::class]);
    }
}

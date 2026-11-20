<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Job;
use App\Entity\UserAccount;
use App\Entity\Worker;
use App\Repository\JobRepository;
use App\Repository\UserAccountRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class WorkerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Full name'])
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
            ->add('startDate', DateType::class, ['label' => 'Start date', 'widget' => 'single_text', 'input' => 'datetime_immutable', 'required' => false])
            ->add('endDate', DateType::class, ['label' => 'End date', 'widget' => 'single_text', 'input' => 'datetime_immutable', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Worker::class]);
    }
}

<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\TaskAllocation;
use App\Entity\Job;
use App\Entity\Project;
use App\Entity\ServiceProvider;
use App\Entity\Worker;
use App\Repository\JobRepository;
use App\Repository\CustomerWorkerAssignmentRepository;
use App\Repository\ServiceProviderRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class TaskAllocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $project = $options['project'];
        $builder
            ->add('job', EntityType::class, [
                'class' => Job::class,
                'choice_label' => static fn (Job $job): string => $job->getLabel() ?? 'Unnamed job',
                'query_builder' => static fn (JobRepository $jobs) => $jobs->createQueryBuilder('job')->andWhere('job.isTrashed = false')->orderBy('job.label'),
            ])
            ->add('provider', EntityType::class, [
                'class' => ServiceProvider::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'No provider',
                'query_builder' => static fn (ServiceProviderRepository $providers) => $providers->createQueryBuilder('provider')->andWhere('provider.isTrashed = false')->orderBy('provider.name'),
            ])
            ->add('worker', EntityType::class, [
                'class' => Worker::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'Unassigned',
                'query_builder' => static fn (CustomerWorkerAssignmentRepository $assignments) => $assignments->createActiveWorkersForProjectQueryBuilder($project),
            ])
            ->add('volume', IntegerType::class, [
                'label' => 'Volume',
                'constraints' => [new Assert\Positive()],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => TaskAllocation::class]);
        $resolver->setRequired('project');
        $resolver->setAllowedTypes('project', Project::class);
    }
}

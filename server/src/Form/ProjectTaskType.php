<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\ProjectTask;
use App\Repository\TaskStateRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProjectTaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Task name', 'trim' => true])
            ->add('startDate', DateType::class, [
                'label' => 'Start date',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('endDate', DateType::class, [
                'label' => 'End date',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('state', EntityType::class, [
                'class' => \App\Entity\TaskState::class,
                'choice_label' => 'label',
                'query_builder' => static fn (TaskStateRepository $repository) => $repository->createQueryBuilder('state')
                    ->andWhere('state.isTrashed = false')
                    ->orderBy('state.label', 'ASC'),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => ProjectTask::class]);
    }
}

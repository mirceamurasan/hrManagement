<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\CustomerWorkerAssignment;
use App\Entity\Worker;
use App\Repository\WorkerRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CustomerWorkerAssignmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('worker', EntityType::class, [
            'class' => Worker::class,
            'choice_label' => 'name',
            'query_builder' => static fn (WorkerRepository $workers) => $workers->createQueryBuilder('worker')->andWhere('worker.isTrashed = false')->orderBy('worker.name'),
        ]);
    }
    public function configureOptions(OptionsResolver $resolver): void { $resolver->setDefaults(['data_class' => CustomerWorkerAssignment::class]); }
}

<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\ContractType;
use App\Entity\Employer;
use App\Entity\EmploymentContract;
use App\Entity\Job;
use App\Entity\Worker;
use App\Repository\ContractTypeRepository;
use App\Repository\EmployerRepository;
use App\Repository\JobRepository;
use App\Repository\WorkerRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EmploymentContractType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('worker', EntityType::class, ['class' => Worker::class, 'choice_label' => 'name', 'query_builder' => static fn (WorkerRepository $r) => $r->createQueryBuilder('w')->andWhere('w.isTrashed = false')->orderBy('w.name')])
            ->add('type', EntityType::class, ['class' => ContractType::class, 'choice_label' => 'name', 'query_builder' => static fn (ContractTypeRepository $r) => $r->createQueryBuilder('t')->andWhere('t.isTrashed = false')->orderBy('t.name')])
            ->add('employer', EntityType::class, ['class' => Employer::class, 'choice_label' => 'name', 'query_builder' => static fn (EmployerRepository $r) => $r->createQueryBuilder('e')->andWhere('e.isTrashed = false')->orderBy('e.name')])
            ->add('job', EntityType::class, ['class' => Job::class, 'choice_label' => static fn (Job $j): string => $j->getLabel() ?? 'Unnamed job', 'query_builder' => static fn (JobRepository $r) => $r->createQueryBuilder('j')->andWhere('j.isTrashed = false')->orderBy('j.label')])
            ->add('comment', TextareaType::class, ['label' => 'Comment', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void { $resolver->setDefaults(['data_class' => EmploymentContract::class]); }
}

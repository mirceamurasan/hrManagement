<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Project;
use App\Entity\ProjectStatus;
use App\Repository\CustomerRepository;
use App\Repository\ProjectStatusRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Project name', 'trim' => true])
            ->add('sapNumber', TextType::class, ['label' => 'SAP number', 'required' => false, 'trim' => true])
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'choice_label' => 'name',
                'query_builder' => static fn (CustomerRepository $repository) => $repository->createQueryBuilder('customer')
                    ->andWhere('customer.isTrashed = false')
                    ->orderBy('customer.name', 'ASC'),
            ])
            ->add('status', EntityType::class, [
                'class' => ProjectStatus::class,
                'choice_label' => 'label',
                'placeholder' => 'No status',
                'required' => false,
                'query_builder' => static fn (ProjectStatusRepository $repository) => $repository->createQueryBuilder('status')
                    ->andWhere('status.isTrashed = false')
                    ->orderBy('status.label', 'ASC'),
            ])
            ->add('comments', TextareaType::class, ['label' => 'Comments', 'required' => false])
            ->add('outsourcing', null, ['label' => 'Outsourcing project', 'required' => false])
            ->add('generalInfo', TextType::class, ['label' => 'General information', 'required' => false])
            ->add('technicalInfo', TextType::class, ['label' => 'Technical information', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Project::class]);
    }
}

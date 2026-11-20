<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\ProjectKeyDate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProjectKeyDateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('date', DateType::class, ['widget' => 'single_text', 'input' => 'datetime_immutable'])
            ->add('details', TextType::class, ['label' => 'Milestone']);
    }
    public function configureOptions(OptionsResolver $resolver): void { $resolver->setDefaults(['data_class' => ProjectKeyDate::class]); }
}

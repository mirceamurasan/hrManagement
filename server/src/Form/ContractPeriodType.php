<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\ContractPeriod;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ContractPeriodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateType::class, ['label' => 'Start date', 'widget' => 'single_text', 'input' => 'datetime_immutable'])
            ->add('endDate', DateType::class, ['label' => 'End date', 'widget' => 'single_text', 'input' => 'datetime_immutable', 'required' => false])
            ->add('dailyRate', IntegerType::class, ['label' => 'Daily rate', 'required' => false])
            ->add('serviceProvider', CheckboxType::class, ['label' => 'External service provider', 'required' => false]);
    }
    public function configureOptions(OptionsResolver $resolver): void { $resolver->setDefaults(['data_class' => ContractPeriod::class]); }
}

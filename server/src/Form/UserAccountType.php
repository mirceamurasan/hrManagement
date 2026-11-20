<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\UserAccount;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UserAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('roles', ChoiceType::class, [
                'choices' => ['Basic user' => 'ROLE_USER', 'Forecast user' => 'ROLE_FORECAST_USER', 'Worker' => 'ROLE_WORKER', 'Project manager' => 'ROLE_PM', 'Unit manager' => 'ROLE_UNIT_MANAGER', 'Administrator' => 'ROLE_ADMIN', 'Super administrator' => 'ROLE_SUPER_ADMIN'],
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
            ->add('active', CheckboxType::class, ['required' => false])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'help' => $options['password_required'] ? 'At least 12 characters.' : 'Leave blank to keep the current password.',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => UserAccount::class, 'password_required' => false]);
        $resolver->setAllowedTypes('password_required', 'bool');
    }
}

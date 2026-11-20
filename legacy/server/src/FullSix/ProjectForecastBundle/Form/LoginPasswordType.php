<?php

namespace FullSix\ProjectForecastBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LoginPasswordType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('plainPassword', 'repeated', array(
            'type' => 'password',
            'required' => true,
            'first_options' => array('label' => 'login.label.password'),
            'second_options' => array('label' => 'login.label.confirmPass'),
            'invalid_message' => 'fos_user.password.mismatch',
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FullSix\ProjectForecastBundle\Entity\Login'
        ));
    }

    public function getName()
    {
        return 'fullsix_projectforecastbundle_loginpasswordtype';
    }
}

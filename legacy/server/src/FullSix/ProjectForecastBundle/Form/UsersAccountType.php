<?php

namespace FullSix\ProjectForecastBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class UsersAccountType extends BaseType
{
    public function __construct() {
        parent::__construct('FullSix\ProjectForecastBundle\Entity\Users');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //parent::buildForm($builder, $options);
        $readonly = $options['data']->isAdLinked();

        $builder
            ->add('usersfirstname', null, array('required' => true, 'read_only' => $readonly, 'label' => "Firstname"))
            ->add('userslastname', null, array('required' => true, 'read_only' => $readonly, 'label' => "Lastname"))
            ->add('usersinitials', null, array('required' => false, 'read_only' => $readonly, 'label' => "Initiales"))
            ->add('email', 'email', array('label' => 'Email', 'read_only' => $readonly))
            ->add('birthDate', 'date', array(
                    'required' => false,
                    'label' => "Date de naissasnce",
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'attr'=> array('class'=>'has_datepicker')
                ))
            ->add('phoneNumber', null, array('required' => false, 'label' => "Phone number"))
            ->add('contactMsn', null, array('required' => false, 'label' => "Contact MSN"))
            ->add('contactFacebook', null, array('required' => false, 'label' => "Contact Facebook"))
            ->add('contactTwitter', null, array('required' => false, 'label' => "Contact Twitter"))
            ->add('message', 'textarea', array(
                    'required' => false,
                    'label' => "Message Personnel"));
    }

    public function getName()
    {
        return 'fullsix_projectforecastbundle_userstype';
    }
}

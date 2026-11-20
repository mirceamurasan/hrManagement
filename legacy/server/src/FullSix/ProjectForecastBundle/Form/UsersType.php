<?php

namespace FullSix\ProjectForecastBundle\Form;

use FullSix\ProjectForecastBundle\Entity\Users;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use FullSix\ProjectForecastBundle\Form\EventListener\AddLoginSubscriber;

class UsersType extends BaseType
{
    private $roles;
    private $readonly;
    private $requiredLogin;
    private $showPass;

    /*
     * @param $requiredLogin : specify if the user's login is mandatory
     * @param $showPass : specify if the password is needed
     *      newAction => password displayed
     *      editAction => no password displayed
     * @param $readonly : specify if the fields are editable
     * */
    public function __construct($roles, $requiredLogin = true, $showPass = true, $readonly = false) {
        parent::__construct('FullSix\ProjectForecastBundle\Entity\Login');
        $this->roles = $roles;
        $this->requiredLogin = $requiredLogin;
        $this->showPass = $showPass;
        $this->readonly = $readonly;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
        //basic information
        ->add('usersfirstname', null, array(
            'required' => true,
            'read_only' => $this->readonly,
            'label' => "Firstname"))
        ->add('userslastname', null, array(
            'required' => true,
            'read_only' => $this->readonly,
            'label' => "Lastname"))
        ->add('usersinitials', null, array(
            'required' => false,
            'read_only' => $this->readonly,
            'label' => "Initiales"))
        ->add('email', 'email', array(
            'label' => 'Email',
            'required' => true,
            'read_only' => $this->readonly))

        //social information : not required
        ->add('birthDate', 'date', array(
            'required' => false,
            'label' => "Birthday",
            'widget' => 'single_text',
            'format' => 'dd-MM-yyyy',
             'attr'=> array('class'=>'has_datepicker')
           ))
        ->add('phoneNumber', null, array(
            'required' => false,
            'label' => "Phone number"))
        ->add('contactMsn', null, array(
            'required' => false,
            'label' => "Contact MSN"))
        ->add('contactFacebook', null, array(
            'required' => false,
            'label' => "Contact Facebook"))
        ->add('contactTwitter', null, array(
            'required' => false,
            'label' => "Contact Twitter"))
        ->add('message', 'textarea', array(
            'required' => false,
            'label' => "Message Personnel"));

        $builder->add('login', new LoginType($this->roles, $this->requiredLogin, $this->showPass, $this->readonly ),
            array('required' => $this->requiredLogin));

        // $builder->addEventSubscriber(new AddLoginSubscriber($builder->getFormFactory()));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FullSix\ProjectForecastBundle\Entity\Users',
            'cascade_validation'  => true,
        ));
    }

    public function getName()
    {
        return 'fullsix_projectforecastbundle_userstype';
    }
}

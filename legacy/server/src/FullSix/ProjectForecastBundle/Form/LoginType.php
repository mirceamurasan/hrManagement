<?php

namespace FullSix\ProjectForecastBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use FullSix\ProjectForecastBundle\Repository\RolesRepository;

class LoginType extends BaseType
{
    private $roles;
    private $readonly;
    private $requiredFields;
    private $showPass;

    /*
     * @param requiredFields : specify if the fields are mandatory
     * @param $showPass : specify if the password is needed
     *      newAction => password required
     *      editAction => no password required
     * @param $readonly : specify if the fields are editable
     * */
    public function __construct($roles, $requiredFields = true, $showPass = true, $readonly = false) {
        parent::__construct('FullSix\ProjectForecastBundle\Entity\Login');
        $this->roles = $roles;
        $this->requiredFields = $requiredFields;
        $this->showPass = $showPass;
        $this->readonly = $readonly;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array(
                    'read_only' => $this->readonly,
                    'required' => $this->requiredFields,
                    'label' => "login.label.username"))
            ->add('expiresAt', 'date',
                array(
                'label' => "login.label.endAccountDate",
                'required' => false, //not mandatory
                'read_only' => $this->readonly,
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'attr'=> array('class'=>'has_datepicker')
                ));

        if($this->showPass){
            $builder->add('plainPassword', 'repeated', array(
                    'type' => 'password',
                    'required' => $this->requiredFields,
                   // 'options' => array('translation_domain' => 'FOSUserBundle'),
                    'first_options' => array('label' => 'login.label.password'),
                    'second_options' => array('label' => 'login.label.confirmPass'),
                    'invalid_message' => 'fos_user.password.mismatch',
                ));
        }

        $builder->add('roles', 'choice', array(
                'label' => 'Role',
                'mapped' => true,
                'expanded' => true,
                'multiple' => true,
                'required' => false,
                'attr' => array('class' => 'checkbox-container'),
                'choices' => $this->roles
              ));
    }

    public function getName()
    {
        return 'fullsix_projectforecastbundle_logintype';
    }
}

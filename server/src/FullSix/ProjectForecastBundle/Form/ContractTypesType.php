<?php

namespace FullSix\ProjectForecastBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContractTypesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contracttypesname', null, array('label' => "common.field.label"))
            ->add('contractTypesIsExternal', null, array('label' => "admin.typeCon.external"))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FullSix\ProjectForecastBundle\Entity\ContractTypes'
        ));
    }

    public function getName()
    {
        return 'fullsix_projectforecastbundle_contracttypestype';
    }
}

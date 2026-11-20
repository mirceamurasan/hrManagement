<?php

namespace FullSix\ProjectForecastBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class LocationsType extends AbstractType
{
        public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Locationsname', null, array('label' => "common.field.label"))
            ->add('Locationsaddress', 'textarea', array(
                'label' => "admin.location.address",
                'required' => false
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FullSix\ProjectForecastBundle\Entity\Locations'
        ));
    }

    public function getName()
    {
        return 'fullsix_projectforecastbundle_locationstype';
    }
}

<?php

namespace FullSix\ProjectForecastBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class KeyDatesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('date', 'date', array(
                'label' => "common.field.date",
                'widget' => 'single_text',
                'required' => true,
                'format' => 'dd-MM-yyyy',
                'attr'=> array('class'=>'has_datepicker'),
                ))
            ->add('details', null, array('label' => "keydates.details"))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'FullSix\ProjectForecastBundle\Entity\KeyDates'
        ));
    }

    public function getName() {
        return 'fullsix_projectforecastbundle_keydatestype';
    }

}

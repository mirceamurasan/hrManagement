<?php

namespace FullSix\ProjectForecastBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class VacationsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startdate', 'date', array(
                'label' => "common.field.startDate",
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'required' => true,
                'attr'=> array('class'=>'has_datepicker start-date'),
                'error_bubbling' => true
            ))
            ->add('enddate', 'date', array(
                'label' => "common.field.endDate",
                'widget' => 'single_text',
                'format' => 'dd-MM-yyyy',
                'required' => true,
                'attr'=> array('class'=>'has_datepicker end-date'),
                'error_bubbling' => true
            ))
            ->add('morning', 'checkbox', array(
                'label' => "worker.vacation.morning",
                'required' => false,
                'attr'=> array('class'=> 'morning'),
                'error_bubbling' => true
            ))
            ->add('afternoon', 'checkbox', array(
                    'label' => 'worker.vacation.afternoon',
                    'required' => false,
                    'attr'=> array('class'=> 'afternoon'),
                    'error_bubbling' => true
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FullSix\ProjectForecastBundle\Entity\Vacations'
        ));
    }

    public function getName()
    {
        return 'fullsix_projectforecastbundle_vacationstype';
    }
}

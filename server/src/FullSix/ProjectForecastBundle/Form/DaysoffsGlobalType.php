<?php

namespace FullSix\ProjectForecastBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DaysoffsGlobalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('DaysoffsGlobalDate', 'date', array(
                    'label' => "Date",
                    'widget' => 'single_text',
                   // 'attr'=> array('class'=>'has_datepicker')
                ))
            ->add('DaysoffsGlobalName', null, array('label' => "Libellé"))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FullSix\ProjectForecastBundle\Entity\DaysoffsGlobal'
        ));
    }

    public function getName()
    {
        return 'fullsix_projectforecastbundle_daysoffsglobaltype';
    }
}

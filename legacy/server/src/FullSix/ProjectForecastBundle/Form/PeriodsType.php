<?php

namespace FullSix\ProjectForecastBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PeriodsType extends AbstractType
{
    private $isExternal;

    public function __construct($isExternal = true)
    {
        $this->isExternal = $isExternal;
    }

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
                    'required' => $this->isExternal,
                    //not required if contract is internal (if null => infinite/boundless)
                    'attr'=> array('class'=>'has_datepicker end-date'),
                    'error_bubbling' => true
                ));
        if($this->isExternal){
            $builder
                ->add('tjm', 'text', array(
                    'label' => "Taux journalier moyen",
                    'required' => true,
                    'attr'=> array('class'=> 'tax'),
                    'error_bubbling' => true
                ));
        }
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FullSix\ProjectForecastBundle\Entity\Periods'
        ));
    }

    public function getName()
    {
        return 'fullsix_projectforecastbundle_periodstype';
    }
}

<?php

namespace FullSix\ProjectForecastBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TasksType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('projectsidprojects', null, array('label' => "Project"))
            ->add('tasksname', null, array('label' => "Task name"))
            ->add('tasksbeginat', 'date', array(
                    'label' => "Start date",
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'attr'=> array('class'=>'has_datepicker')
                ))
            ->add('tasksendat', 'date', array(
                    'label' => "End date",
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'attr'=> array('class'=>'has_datepicker')
                ))
            //->add('tasksorder', null, array('label' => "Ordre"))
            //->add('statesidstates', null, array('label' => "Etat"))
            //->add('lotsidlots', null, array('label' => "Lot"))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FullSix\ProjectForecastBundle\Entity\Tasks'
        ));
    }

    public function getName()
    {
        return 'fullsix_projectforecastbundle_taskstype';
    }
}

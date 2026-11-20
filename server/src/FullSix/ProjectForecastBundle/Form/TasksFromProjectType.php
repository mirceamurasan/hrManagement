<?php

namespace FullSix\ProjectForecastBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TasksFromProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('projectsidprojects', 'hidden', array('property_path' => 'projectsidprojects.id'))
            ->add('tasksname', null, array('label' => "Task name"))
            ->add('tasksbeginat', null, array('label' => "Start date", 'widget' => 'single_text', 'format' => 'dd-MM-yyyy'))
            ->add('tasksendat', null, array('label' => "Finish date", 'widget' => 'single_text', 'format' => 'dd-MM-yyyy'))
            //->add('tasksorder', 'hidden', array('data' => '1'))
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

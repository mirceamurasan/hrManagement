<?php

namespace FullSix\ProjectForecastBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TasksloadsType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('tasksidtasks')
                ->add('jobsidjobs', null, array('label' => "Métier"))
                ->add('tasksloadsvolume', null, array('label' => "Volume"))
                ->add('tasksloadsworkers', null, array('label' => "Affectation"))
                ->add('providersidproviders', null, array('label' => "Prestataire"))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'FullSix\ProjectForecastBundle\Entity\Tasksloads'
        ));
    }

    public function getName() {
        return 'tasksloadsform';
    }

}

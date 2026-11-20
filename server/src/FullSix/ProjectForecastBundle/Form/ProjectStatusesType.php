<?php

namespace FullSix\ProjectForecastBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProjectStatusesType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('projectstatuseslabel', null, array('label' => "common.field.name"))
                ->add('projectstatusesisfinal', null, array('label' => "admin.status.finished"))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'FullSix\ProjectForecastBundle\Entity\ProjectStatuses'
        ));
    }

    public function getName() {
        return 'fullsix_projectforecastbundle_projectstatusestype';
    }

}
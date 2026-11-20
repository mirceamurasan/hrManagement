<?php

namespace FullSix\ProjectForecastBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use FullSix\ProjectForecastBundle\Repository\WorkersRepository;

class TasksloadsFromProjectType extends AbstractType
{

    protected $workers;

    public function __construct($workers = null) {
        $this->workers = $workers;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('tasksidtasks', 'hidden', array('property_path' => 'tasksidtasks.id'))
            ->add('jobsidjobs', null, array(
                'class' => 'FullSixProjectForecastBundle:Jobs',
                'label' => 'Métier',
                'required' => true,
                'query_builder' => function (EntityRepository $er) {
                        return $er
                            ->createQueryBuilder('j')
                            ->where('j.isTrashed = 0');
                    }
            ))
            ->add('tasksloadsvolume', null, array('label' => "Volume"))
            ->add('tasksloadsworkers', 'entity', array(
                'label' => "Affectation",
                'class' => 'FullSix\ProjectForecastBundle\Entity\Workers',
                'choices' => $this->workers,
                'required' => false,
                ))
            ->add('providersidproviders', null, array(
                'class' => 'FullSixProjectForecastBundle:Providers',
                'label' => 'Métier',
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                        return $er
                            ->createQueryBuilder('j')
                            ->where('j.isTrashed = 0');
                    }
            ));
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

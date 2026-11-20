<?php

namespace FullSix\ProjectForecastBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class JobsFilterType extends AbstractType
{
    protected $jobsList;

    public function __construct($jobsList)
    {
        $this->jobsList = $jobsList;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('startDate', 'hidden', array(
                    'mapped' => false,
                ))
                ->add('jobsSelect', 'choice', array(
                    'label' => 'Trier par métier',
                    'choices' => $this->jobsList,
                    'data' => array_keys($this->jobsList),
                    'multiple' => true,
                ))
                ->add('prevWeek', 'submit', array(
                    'label' => '<< Previous week',
                    'attr' => array(
                        'class' => 'btn-small btn-primary'
                    ),
                ))
                ->add('nextWeek', 'submit', array(
                    'label' => 'Next week >>',
                    'attr' => array(
                        'class' => 'btn-small btn-primary',
                    ),
                ))
                ->add('recherche', 'submit', array(
                    'attr' => array(
                        'class' => 'btn-small btn-primary',
                    ),
                ))
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this, 'onPreSubmit'));
    }

    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $nextPeriod = null;

        $formDate = new \DateTime($data['startDate']);
        // check if previous week button was clicked
        if (isset($data['prevWeek'])) {
            $nextPeriod = 'prevWeek';
        }

        // check if next week button was clicked
        if (isset($data['nextWeek'])) {
            $nextPeriod = 'nextWeek';
        }

        //update view period according to submit button
        if ($nextPeriod) {
            $monday = ($nextPeriod == 'prevWeek') ? $formDate->modify("-1 week") : $formDate->modify("+1 week");
            $data['startDate'] = $monday->format('Y-m-d');
            $event->setData($data);
        }
    }

    public function getName()
    {
        return 'fullsix_projectforecastbundle_jobsfiltertype';
    }
}

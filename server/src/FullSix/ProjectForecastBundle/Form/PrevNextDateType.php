<?php

namespace FullSix\ProjectForecastBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class PrevNextDateType extends AbstractType
{
    protected $workers;

    public function __construct($workers = null)
    {
        $this->workers = $workers;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('startDate', 'hidden', array(
                    'mapped' => false,
                    'attr' => array(
                        'class' => 'fStartDate'
                    ),
                ))
                ->add('prevWeek', 'submit', array(
                    'label' => 'reporting.weekly.buttons.prevWeek',
                    'attr' => array(
                        'class' => 'btn-small btn-primary'
                    ),
                ))
                ->add('nextWeek', 'submit', array(
                    'label' => 'reporting.weekly.buttons.nextWeek',
                    'attr' => array(
                        'class' => 'btn-small btn-primary',
                    ),
                ))
        ;

        if ($this->workers) {
            $builder
                    ->add('filter', 'submit', array(
                        'label' => 'reporting.weekly.buttons.filter',
                        'attr' => array(
                            'class' => 'btn report-filter',
                )))
                    ->add('workers', 'choice', array(
                        'label' => 'reporting.weekly.buttons.filter',
                        'choices' => $this->workers,
                        'required' => false,
            ));
        }

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
        return 'fullsix_projectforecastbundle_prevnextdatetype';
    }
}

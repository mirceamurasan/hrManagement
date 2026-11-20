<?php

namespace FullSix\ProjectForecastBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DaysoffsType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $date = $options['attr']['date'];
        $entity = $options['attr']['entity'];

        $tab_daysoffs = array();
        foreach ($entity as $entity) {
            if ($entity->getDaysoffsMorning()) $tab_daysoffs[$entity->getDaysoffsDate()->format('d-m-Y')]['m'] = true;
            if ($entity->getDaysoffsAfternoon()) $tab_daysoffs[$entity->getDaysoffsDate()->format('d-m-Y')]['a'] = true;
        }

        $current_year = $date->format('Y');
        $builder->add('date', 'hidden', array('data' => $date->format("Y_m_d")));
        while ($date->format('Y') ==  $current_year) {
            if (key_exists($date->format("d-m-Y"), $tab_daysoffs) && key_exists('m', $tab_daysoffs[$date->format("d-m-Y")])) {
                $builder->add('m'.$date->format("Ymd"), 'checkbox', array('data' => true, 'required' => false));
            } else {
                $builder->add('m'.$date->format("Ymd"), 'checkbox', array('required' => false));
            }
            if (key_exists($date->format("d-m-Y"), $tab_daysoffs) && key_exists('a', $tab_daysoffs[$date->format("d-m-Y")])) {
                $builder->add('a'.$date->format("Ymd"), 'checkbox', array('data' => true, 'required' => false));
            } else {
                $builder->add('a'.$date->format("Ymd"), 'checkbox', array('required' => false));
            }

            $date->add(new \DateInterval('P1D'));
        }
    }

    public function getName()
    {
        return 'fullsix_projectforecastbundle_daysoffstype';
    }
}

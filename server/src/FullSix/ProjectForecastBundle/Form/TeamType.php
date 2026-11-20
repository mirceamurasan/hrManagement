<?php

namespace FullSix\ProjectForecastBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (array_key_exists('data', $options)) {
            $currLoid = $options['data']->getTeamslocation() ?  $options['data']->getTeamslocation()->getId() : null;
        }else{
            $currLoid =  null;
        }

         $builder
                ->add('teamsname', null, array(
                     'required' => true,
                     'label' => "common.field.name"))
                ->add('teamslocation', 'entity', array(
                    'label' => 'admin.location.label',
                    'class' => 'FullSixProjectForecastBundle:Locations',
                    'property' => 'locationsname',
                    'required' => true,
                    'query_builder' => function (EntityRepository $er) use ($currLoid) {
                         $query = $er
                             ->createQueryBuilder('l')
                             ->where('l.isTrashed = 0');
                         if ($currLoid) {
                             $query->orWhere('l.id = :id')
                                 ->setParameter(":id", $currLoid);
                         }
                         return $query;
                     }
                ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FullSix\ProjectForecastBundle\Entity\Teams',
        ));
    }

    public function getName()
    {
        return 'fullsix_projectforecastbundle_teamstype';
    }
}

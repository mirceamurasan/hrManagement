<?php

namespace FullSix\ProjectForecastBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class CustomersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (array_key_exists('data', $options)) {
            $currAgid = $options['data']->getAgenciesidagencies() ?  $options['data']->getAgenciesidagencies()->getId() : null;
        }else{
            $currAgid =  null;
        }

        $builder
            ->add('customersname', null, array('label' => "Customer's name"))
            ->add('agenciesidagencies', null, array(
                'class' => 'FullSixProjectForecastBundle:Agencies',
                'label' => 'admin.agency.label',
                'property' => 'agenciesname',
                'query_builder' => function (EntityRepository $er) use ($currAgid) {
                        $query = $er
                            ->createQueryBuilder('ag')
                            ->where('ag.isTrashed = 0');
                        if ($currAgid) {
                            $query->orWhere('ag.id = :id')
                                ->setParameter(":id", $currAgid);
                        }
                        return $query;
                    }
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FullSix\ProjectForecastBundle\Entity\Customers'
        ));
    }

    public function getName()
    {
        return 'fullsix_projectforecastbundle_customerstype';
    }
}

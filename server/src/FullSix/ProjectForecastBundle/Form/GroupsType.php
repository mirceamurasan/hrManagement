<?php

namespace FullSix\ProjectForecastBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FullSix\ProjectForecastBundle\Repository\UsersRepository;

class GroupsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array('label' => "Nom du groupe"))
            ->add(
                'groupUsers',
                'entity',
                array(
                    'class' => 'FullSix\ProjectForecastBundle\Entity\Users',
                    'multiple' => true,
                    'required' => false,
                    'expanded' => true,
                    'label' => "Membres du groupe",
                    'query_builder' => function (UsersRepository $er) {
                        return $er
                            ->createQueryBuilder('u')
                            ->orderBy('u.userslastname', 'ASC');
                    }
                )
            )
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FullSix\ProjectForecastBundle\Entity\Groups'
        ));
    }

    public function getName()
    {
        return 'fullsix_projectforecastbundle_groupstype';
    }
}

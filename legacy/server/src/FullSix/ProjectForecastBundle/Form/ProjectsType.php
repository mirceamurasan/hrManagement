<?php

namespace FullSix\ProjectForecastBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface;
use FullSix\ProjectForecastBundle\Repository\UsersRepository;
use FullSix\ProjectForecastBundle\Repository\LoginRepository;

class ProjectsType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('projectsname', null, array('label' => "Project name"))
            ->add('customersidcustomers', null, array(
                'class' => 'FullSixProjectForecastBundle:Customers',
                'label' => 'Client',
                'required' => true,
                'query_builder' => function (EntityRepository $er) {
                        return $er
                            ->createQueryBuilder('ag')
                            ->where('ag.isTrashed = 0');
                        //if non admin will be able to create project => filter clients
                    }
            ))
            ->add('projectstatus', null, array(
                'class' => 'FullSixProjectForecastBundle:ProjectStatuses',
                'label' => 'Status',
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                        return $er
                            ->createQueryBuilder('ag')
                            ->where('ag.isTrashed = 0');
                    }
            ))
            ->add('projectssuccess', 'choice', array('choices' => array('0'=>'0 %', '10'=>'10 %', '20'=>'20 %', '30'=>'30 %', '40'=>'40 %', '50'=>'50 %', '60'=>'60 %', '70'=>'70 %', '80'=>'80 %', '90'=>'90 %', '100'=>'100 %'), 'label' => "Chance d'obtenir le budget"))
            ->add('isoutsourcing', 'checkbox', array('label' => "Outsourcing project", 'required' => false))
            ->add('infotech', 'textarea', array(
                    'required' => false,
                    'label' => "project.infoTech"))
            ->add('infogeneral', 'textarea', array(
                    'required' => false,
                    'label' => "project.infoGlobal"))
            ->add('projectscomments', 'textarea', array(
                    'required' => false,
                    'label' => "Comments"));
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'FullSix\ProjectForecastBundle\Entity\Projects'
        ));
    }

    public function getName() {
        return 'fullsix_projectforecastbundle_projectstype';
    }

}

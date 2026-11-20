<?php

namespace FullSix\ProjectForecastBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use FullSix\ProjectForecastBundle\Entity\ContractTypes;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use FullSix\ProjectForecastBundle\Form\PeriodsType;

class ContractsType extends BaseType
{
    private $withperiod;

    public function __construct($withperiod = false)
    {
        parent::__construct('FullSix\ProjectForecastBundle\Entity\Contracts');
        $this->withperiod = $withperiod;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (array_key_exists('data', $options)) {
            $currCTid = $options['data']->getContractType() ?  $options['data']->getContractType()->getId() : null;
            $currEmid = $options['data']->getEmployer() ?  $options['data']->getEmployer()->getId() : null;
            $currJobid = $options['data']->getIdjob() ?  $options['data']->getIdjob()->getId() : null;
        }else{
            $currCTid =  null;
            $currEmid =  null;
            $currJobid = null;
        }

        $builder
            ->add('workerfirstname', null, array(
                'mapped' => false,
                'label' => 'user.firstName',
                'read_only' => true
                ))
            ->add('workerlastname', null, array(
                'mapped' => false,
                'label' => 'user.lastName',
                'read_only' => true
                ))
            ->add('contracttype', 'entity', array(
                'class' => 'FullSixProjectForecastBundle:ContractTypes',
                'label' => 'admin.typeCon.label',
                'property' => 'contracttypesname',
                'query_builder' => function (EntityRepository $er) use ($currCTid) {
                    $query = $er
                        ->createQueryBuilder('ct')
                        ->where('ct.isTrashed = 0');
                    if ($currCTid) {
                        $query->orWhere('ct.id = :id')
                            ->setParameter(":id", $currCTid);
                    }
                    return $query;
                }
            ))
            ->add('employer', 'entity', array(
                'class' => 'FullSixProjectForecastBundle:Employers',
                'label' => 'admin.employer.label',
                'property' => 'employersname',
                'query_builder' => function (EntityRepository $er) use ($currEmid) {
                        $query = $er
                            ->createQueryBuilder('empl')
                            ->where('empl.isTrashed = 0');
                        if ($currEmid) {
                            $query->orWhere('empl.id = :id')
                                ->setParameter(":id", $currEmid);
                        }
                        return $query;
                    }
            ))
            ->add('idjob', 'entity', array(
                'class' => 'FullSixProjectForecastBundle:Jobs',
                'property' => 'jobslabel',
                'label' => 'admin.job.label',
                'required' => true,
                'attr'=> array('class'=> 'job'),
                'error_bubbling' => true,
                'query_builder' => function (EntityRepository $er) use ($currJobid) {
                        $query = $er
                            ->createQueryBuilder('j')
                            ->where('j.isTrashed = 0');
                        if ($currJobid) {
                            $query->orWhere('j.id = :id')
                                ->setParameter(":id", $currJobid);
                        }
                        return $query;
                    }
            ))
            ->add('bdc', 'text', array(
                'label' => "Bon de commande",
                'required' => false
            ))
            ->add('comment', 'textarea', array(
                'label' => "Comment",
                'required' => false,
            ))
        ;

        if ($this->withperiod) {
            $builder->add('periods', 'collection',array(
                'type' => new PeriodsType(),
                'allow_add' => true,
                ));
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FullSix\ProjectForecastBundle\Entity\Contracts',
            'cascade_validation' => true
        ));
    }

    public function getName()
    {
        return 'fullsix_projectforecastbundle_contractstype';
    }
}

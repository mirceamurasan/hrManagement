<?php

namespace FullSix\ProjectForecastBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FullSix\ProjectForecastBundle\Form\UsersType;
use FullSix\ProjectForecastBundle\Entity\Workers;
use FullSix\ProjectForecastBundle\Form\EventListener\AddLocationFieldSubscriber;
use Doctrine\ORM\EntityRepository;

class WorkersType extends AbstractType
{

    private $roles;
    private $readonly;
    private $requiredLogin;
    private $showPass;

    /*
     * @param $requiredLogin : specify if the user's login is mandatory
     * @param $showPass : specify if the password is needed
     *      newAction => password displayed
     *      editAction => no password displayed
     * @param $readonly : specify if the fields are editable
     * */
    public function __construct($roles, $requiredLogin = false, $showPass = true, $readonly = false) {
        $this->roles = $roles;
        $this->requiredLogin = $requiredLogin;
        $this->showPass = $showPass;
        $this->readonly = $readonly;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (array_key_exists('data', $options)) {
            $currLoid = $options['data']->getLocation() ?  $options['data']->getLocation()->getId() : null;
            $currTeamId = $options['data']->getTeam() ?  $options['data']->getTeam()->getId() : null;
        }else{
            $currLoid =  null;
            $currTeamId =  null;
        }

        $builder->add('user', new UsersType($this->roles, $this->requiredLogin, $this->showPass,  $this->readonly),
            array('required' => true));

        $builder
            ->add('location', 'entity', array(
                'disabled' => $currTeamId ? true : false,
                'empty_value' => '',
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
            ))
            ->add('team', 'entity', array(
                'empty_value' => '',
                'class' => 'FullSixProjectForecastBundle:Teams',
                'property' => 'teamsname',
                'required' => false,
                'query_builder' => function (EntityRepository $er) use ($currTeamId) {
                        $query = $er
                            ->createQueryBuilder('t')
                            ->where('t.isTrashed = 0');
                        if ($currTeamId) {
                            $query->orWhere('t.id = :id')
                                ->setParameter(":id", $currTeamId);
                        }
                        return $query;
                    }
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'FullSix\ProjectForecastBundle\Entity\Workers',
            'cascade_validation'  => true,
        ));
    }

    public function getName()
    {
        return 'fullsix_projectforecastbundle_workerstype';
    }
}

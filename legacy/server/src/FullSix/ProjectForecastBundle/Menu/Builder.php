<?php

namespace FullSix\ProjectForecastBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use FullSix\ProjectForecastBundle\Entity\Jobs;

class Builder extends ContainerAware {

    /**
     *
     * @var \Knp\Menu\ItemInterface
     */
    private $_menu;

    /**
     * Init menu with root items
     *
     * @param \Knp\Menu\FactoryInterface $factory
     * @param array $options
     * @return \FullSix\ProjectForecastBundle\Menu\Builder
     */
    private function _initMenu(FactoryInterface $factory, array $options) {
        $this->_menu = $factory->createItem('root');
        $this->_menu->setAttributes(array('class' => 'nav'));

        $this->_menu->addChild('home', array(
            'route' => 'home',
            'label' => 'Home'
        ));

        $login = $this->container->get('security.context')->getToken()->getUser();
        if ($this->container->get('security.context')->isGranted('ROLE_ADMIN') || $login->getUser()->getWorker()) {
            $this->_menu->addChild('projects', array(
                    'route' => 'projects',
                    'label' => 'project.title'
                ));
            if ($this->container->get('security.context')->isGranted('ROLE_PM')) {
                $this->_menu->addChild('customers', array(
                        'route' => 'customers',
                        'label' => 'client.title'
                    ));

                if ($this->container->get('security.context')->isGranted('ROLE_PM')){
                    $this->_menu->addChild('providers', array(
                            'route' => 'providers',
                            'label' => 'provider.title'
                        ));
                }
            }

            $workers = $this->_menu->addChild('worker.yearbook');
            $workers->addChild('workers', array(
                    'route' => 'workers',
                    'label' => 'worker.yearbook'
                ));
            $workers->addChild('teams', array(
                    'route' => 'teams',
                    'label' => 'team.label'
                ));
            if ($this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
                $workers->addChild('users', array(
                        'route' => 'users',
                        'label' => 'user.title'
                    ));
            }

            $workers->setAttributes(array(
                    'class' => 'dropdown'
                ));
            $workers->setLinkAttributes(array(
                    'class' => 'dropdown-toggle',
                    'data-toggle' => 'dropdown'
                ));
        }

        return $this;
    }

    /**
     * NavBar Menu
     *
     * @param \Knp\Menu\FactoryInterface $factory
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    public function mainMenu(FactoryInterface $factory, array $options) {
        $this->_initMenu($factory, $options);

        $login = $this->container->get('security.context')->getToken()->getUser();
        if ($this->container->get('security.context')->isGranted('ROLE_PM')) {
            $reporting = $this->_menu->addChild('reporting.title');

            $em = $this->container->get('doctrine');
            $entity = $em->getRepository('FullSixProjectForecastBundle:Jobs')->findAll();
            foreach ($entity as $job) {
                if(!$job->getIsTrashed()){
                    $reporting->addChild('workload_'.$job->getId(), array(
                        'route' => 'workload',
                        'routeParameters' => array('jobid' => $job->getId()),
                        'label' => 'Plan de charge "'.$job->getJobslabel().'"'
                    ));
                }
            }
            $reporting->addChild('reporting_revuehebdo', array(
                'route' => 'reporting_revuehebdo',
                'label' => 'reporting.weekly.view'
            ));
            $reporting->addChild('reporting_daysoffs', array(
                'route' => 'reporting_daysoffs',
                'label' => 'reporting.absences.title'
            ));
            if ($this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
                $reporting->addChild('reporting_projectimport', array(
                    'route' => 'project_import_reports',
                    'label' => 'reporting.importReports.projects'
                ));
                $reporting->addChild('users_import_reports', array(
                        'route' => 'users_import_reports',
                        'label' => 'reporting.importReports.users'
                ));
            }

            $reporting->setAttributes(array(
                'class' => 'dropdown'
            ));
            $reporting->setLinkAttributes(array(
                'class' => 'dropdown-toggle',
                'data-toggle' => 'dropdown'
            ));
        }

        if (true === $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            $administration = $this->_menu->addChild('Administration');

            $administration->addChild('groups', array(
                'route' => 'groups',
                'label' => 'admin.groups.title'
            ));
            $administration->addChild('agencies', array(
                'route' => 'agencies',
                'label' => 'admin.agency.title'
            ));

             $administration->addChild('employers', array(
                'route' => 'employers',
                'label' => 'admin.employer.title'
            ));

            $administration->addChild('jobs', array(
                'route' => 'jobs',
                'label' => 'admin.job.title'
            ));
            $administration->addChild('locations', array(
                'route' => 'locations',
                'label' => 'admin.location.title'
            ));
            $administration->addChild('contracttypes', array(
                'route' => 'contracttypes',
                'label' => 'admin.typeCon.title'
            ));
            $administration->addChild('projectstatuses', array(
                'route' => 'projectstatuses',
                'label' => 'admin.status.title'
            ));
            $administration->addChild('daysoffsglobal', array(
                'route' => 'daysoffsglobal',
                'label' => 'admin.pbHol.title'
            ));
            $administration->addChild('roles', array(
                'route' => 'roles',
                'label' => 'Roles'
            ));

            $administration->setAttributes(array(
                'class' => 'dropdown'
            ));
            $administration->setLinkAttributes(array(
                'class' => 'dropdown-toggle',
                'data-toggle' => 'dropdown'
            ));
        }

        return $this->_menu;
    }

    /**
     * SideBar Menu
     *
     * @param \Knp\Menu\FactoryInterface $factory
     * @param array $options
     * @return \Knp\Menu\ItemInterface
     */
    public function sideBar(FactoryInterface $factory, array $options) {
        $this->_initMenu($factory, $options);
        $this->_menu->setAttribute('class', 'nav nav-list');
        $menuItems = $this->_menu->getChildren();
        foreach ($menuItems as $item) {
            $item->setAttribute('class', 'nav-header');
            switch ($item->getName()) {
                case 'administration':
                case 'home':
                    break;
                case 'reporting':
                    $item->addChild('Projects in progress', array(
                        'route' => 'projects_inprogress'
                    ));
                    $item->addChild('Plans load', array(
                        'route' => 'workload'
                    ));
                    break;
                case 'projects':
                    $item->addChild('Added', array(
                        'route' => 'projects_new'
                    ));
                    $item->addChild('Listing', array(
                        'route' => 'projects'
                    ));
                    break;
                default:
                    $item->addChild('Added', array(
                        'route' => strtolower($item->getName()) . '_new'
                    ));
                    $item->addChild('Listing', array(
                        'route' => strtolower($item->getName())
                    ));
                    break;
            }
        }

        return $this->_menu;
    }

}

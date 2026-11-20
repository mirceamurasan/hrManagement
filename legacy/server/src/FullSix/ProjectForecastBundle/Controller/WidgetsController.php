<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Fullsix\ProjectForecastBundle\Entity\Daysoffs;

/**
 * Widgets controller.
 *
 * @Route("/widgets")
 */
class WidgetsController extends Controller
{

    /**
     * @Route("/", name="widgets")
     */
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('home'));
    }

    /**
     * @Route("/projects-update", name="widget_projects_update")
     * @Template()
     */
    public function projectsUpdateAction()
    {
        $currentLogin = $this->getUser();
        // for admin do not filter
        if (true === $this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
      // if (in_array('ROLE_ADMIN', $currentLogin->getRoles()) || in_array('ROLE_SUPER_ADMIN', $currentLogin->getRoles())) {
            $worker = null;
        // for workers show only their projects
        } else if($currentLogin->getUser()->getWorker()) {
            $worker = $currentLogin->getUser()->getWorker();
        // for users, dont show anything
        } else {
            return array('output' => array());
        }

        $output = $this->getDoctrine()->getRepository('FullSixProjectForecastBundle:Projects')->findProjectsToUpdate(15, $worker);
        return array('output' => $output);
    }

    /**
     * @Route("/absence", name="widget_absence")
     * @Template()
     */
    public function absenceAction()
    {
        $current_date = new \DateTime();
        $next_date = clone $current_date;
        $next_date->add(new \DateInterval('P15D'));

        $days_off = $this->getDoctrine()->getRepository('FullSixProjectForecastBundle:Daysoffs')->findAllDaysoffsBetweenTwoDates($current_date, $next_date);
        $output = array();
        $petits_calculs = array();
        foreach($days_off as $day_off)
        {
            if(! isset($output[ $day_off->getUser()->getId() ]))
                $output[ $day_off->getUser()->getId() ] = array(
                    'person' => $day_off->getUser()->getCompleteName(),
                    'function' => $day_off->getUser()->getJob()->getLabel()
                    );

            if(! isset($petits_calculs[ $day_off->getUser()->getId() ]))
                $petits_calculs[ $day_off->getUser()->getId() ] = array('days' => 0, 'back' => new \DateTime(), 'start' => null);

            $petits_calculs[ $day_off->getUser()->getId() ]['days'] += intval( (int)$day_off->getDaysoffsMorning() + (int)$day_off->getDaysoffsAfternoon() );

            if($day_off->getDaysoffsDate() && $day_off->getDaysoffsDate() > $petits_calculs[ $day_off->getUser()->getId() ]['back'])
                $petits_calculs[ $day_off->getUser()->getId() ]['back'] = $day_off->getDaysoffsDate();

            if($day_off->getDaysoffsDate() && is_null($petits_calculs[ $day_off->getUser()->getId() ]['start']) )
                $petits_calculs[ $day_off->getUser()->getId() ]['start'] = $day_off->getDaysoffsDate();
        }

        foreach( $output as $key => $val)
        {
            $output[$key]['days'] = $petits_calculs[$key]['days'] / 2;
            $output[$key]['start'] = $petits_calculs[$key]['start'];
            $output[$key]['back'] = $petits_calculs[$key]['back'];
        }

        return array('output' => $output, 'today' => $current_date);
    }

    /**
     * @Route("/new-projects", name="widget_new_projects")
     * @Template()
     */
    public function newProjectsAction()
    {
        $output = array();

        return array('output' => $output);
    }

    /**
     * @Route("/deadlines-projects", name="widget_deadlines_projects")
     * @Template()
     */
    public function deadlinesProjectsAction()
    {
        $output = $this->getDoctrine()->getRepository('FullSixProjectForecastBundle:Projects')->findNoTrashProjectsByCotechSupervisorOrCotechOperationalAndLastTaskDateDaysLimit($this->getUser()->getId());

        return array('output' => $output);
    }

    /**
     * @Route("/projects-load", name="widget_projects_load")
     * @Template()
     */
    public function projectsLoadAction()
    {
        $output = array();

        return array('output' => $output);
    }

}
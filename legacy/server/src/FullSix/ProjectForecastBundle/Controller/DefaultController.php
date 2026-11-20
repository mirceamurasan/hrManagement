<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction()
    {
        $currentLoginUser = $this->getUser()->getUser();
        if ($currentLoginUser->getWorker() == null)
        {
            return array(
                'isWorker' => false, // lazy one
            );
        }
        $em = $this->getDoctrine()->getManager();
        $monday = new \DateTime("monday this week");
        $workerId = $currentLoginUser->getWorker() ? $currentLoginUser->getWorker()->getId() : null;

        $projects = $em->getRepository('FullSixProjectForecastBundle:Projects')->
                findNoTrashProjectsByWorkerAndTimePeriod($workerId, $monday, new \DateTime());
        $projectsIds = array(0);
        foreach ($projects as $project) {
            $projectsIds[] = $project->getId();
        }

        $relatedTasks = $this->get('fullsix_projectforecast.project_helper')->getProjectsRelatedTasksByWorkerArray($projectsIds, $workerId);

        return array(
            'projects' => $projects,
            'tasks' => $relatedTasks['tasks'],
            'tasksloads' => $relatedTasks['tasksLoads'],
            'out_tasksloads' => $relatedTasks['outTasksLoads'],
            'isWorker' => true,
        );
    }

    /**
     * @Route("/header")
     * @Template()
     */
    public function headerAction()
    {
        return array();
    }

    /**
     * @Route("/sidebar")
     * @Template()
     */
    public function sidebarAction()
    {
        return array();
    }

    /**
     * @Route("/about", name="about")
     * @Template()
     */
    public function aboutAction()
    {
        return array();
    }

    /**
     * @Route("/contact", name="contact")
     * @Template()
     */
    public function contactAction()
    {
        return array();
    }

    /**
     * @Route("/migration", name="migration")
     * @Template()
     */
    public function migrationAction()
    {
        if (false === $this->container->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            throw new ForbiddenHttpException();
        } else {
            $em = $this->getDoctrine()->getManager();
            $projects = $em->getRepository('FullSixProjectForecastBundle:Projects')->findAll();
            foreach ($projects as $project) {
                $project->isOutsourcing();
                $em->persist($project);
            }
            $em->flush();
            return array();
        }
    }

}

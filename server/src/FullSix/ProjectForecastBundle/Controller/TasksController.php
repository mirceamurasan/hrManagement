<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FullSix\ProjectForecastBundle\Entity\Tasks;
use FullSix\ProjectForecastBundle\Form\TasksType;
use FullSix\ProjectForecastBundle\Form\TasksFromProjectType;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Tasks controller.
 *
 * @Route("/tasks")
 */
class TasksController extends Controller {

    /**
     * Lists all Tasks entities.
     *
     * @Route("/", name="tasks")
     * @Template()
     */
    public function indexAction() {
        $source = new Entity('FullSixProjectForecastBundle:Tasks');
        $grid = $this->get('grid');
        $grid->setSource($source);

        $grid->setActionsColumnSeparator("&nbsp;&nbsp;");
        $rowAction_show = new RowAction("book", "tasks_show", false, "_self", array("title" => "Show"));
        $rowAction_show->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_show);

        $rowAction_edit = new RowAction("edit", "tasks_edit", false, "_self", array("title" => "Edit"));
        $rowAction_edit->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_edit);

        return $grid->getGridResponse('FullSixProjectForecastBundle::grid.html.twig',
            array(
                'title' => 'Tasks',
                'entity_new' => "tasks_new"
                )
            );
    }

    /**
     * Finds and displays a Tasks entity.
     *
     * @Route("/{id}/show", name="tasks_show")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Tasks')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tasks entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Tasks entity.
     *
     * @Route("/new", name="tasks_new")
     * @Template()
     */
    public function newAction() {
        $entity = new Tasks();
        $form = $this->createForm(new TasksType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Tasks entity.
     *
     * @Route("/newfromproject", name="tasksfromproject_new")
     * @Template()
     */
    public function newfromprojectAction($id) {
        $entity = new Tasks();

        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('FullSixProjectForecastBundle:Projects')->find($id);

        $entity->setProjectsidprojects($project);
        $form = $this->createForm(new TasksFromProjectType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Tasks entity.
     *
     * @Route("/newfromprojectresponsive", name="tasksfromproject_new_responsive")
     * @Template()
     */
    public function newfromprojectresponsiveAction($project_id) {
        $entity = new Tasks();

        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('FullSixProjectForecastBundle:Projects')->find($project_id);

        $entity->setProjectsidprojects($project);
        $form = $this->createForm(new TasksFromProjectType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Tasks entity.
     *
     * @Route("/create/project/task/{projectId}", name="tasks_create_create_from_project")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:Tasks:new.html.twig")
     */
    public function createFromProjectAction(Request $request, $projectId) {
        $entity = new Tasks();
        $entity->setTaskscreatedby($this->getUser());

        $form = $this->createForm(new TasksType(), $entity);

        //$this->tasksbeginat = \DateTime::createFromFormat('j-m-Y', $tasksbeginat);
        //tasksbeginat
        //tasksendat
        $form_values = $request->request->get('fullsix_projectforecastbundle_taskstype');

        $form->bind($form_values);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $project = $em->getRepository('FullSixProjectForecastBundle:Projects')->find($projectId);
            $ordre = $em->getRepository('FullSixProjectForecastBundle:Tasks')->findMaxOrdreByProjectId($projectId)+1;
            $entity->setTasksorder($ordre);
            $dates = $em->getRepository('FullSixProjectForecastBundle:Projects')->findDatesByProjetcTasks($projectId);
            if ($dates['maxDate'] == '') {
                $project->setProjectfirsttasktdate($entity->getTasksbeginat());
                $project->setProjectlasttasktdate($entity->getTasksendat());
            } else {
                if ($entity->getTasksbeginat() < $minDate = new \DateTime($dates['minDate'])) {
                    $entity->getProjectsidprojects()->setProjectfirsttasktdate($entity->getTasksbeginat());
                } else {
                    $project->setProjectfirsttasktdate($minDate);
                }
                if ($entity->getTasksendat() > $maxDate = new \DateTime($dates['maxDate'])) {
                    $project->setProjectlasttasktdate($entity->getTasksendat());
                } else {
                   $project->setProjectlasttasktdate($maxDate);
                }
            }
            $project->setProjectsupdated();

            $em->persist($entity);
            $em->flush();

            $response = new Response( json_encode( array('status' => 'success',
                'render' =>  $this->renderView("FullSixProjectForecastBundle:Tasks:_item.html.twig", array(
                    'task' => $entity,
                    'cycle' => 'cycle-not-defined'
                )))));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        $response = new Response( json_encode( array('status' => 'error',
            'render' =>  $this->renderView("FullSixProjectForecastBundle:Tasks:newfromprojectresponsive.html.twig",
                        array(
                            'project_id' => $projectId,
                            'entity' => $entity,
                            'form' => $form->createView(),
                        )))));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
    }

    /**
     * Creates a new Tasks entity.
     *
     * @Route("/create", name="tasks_create")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:Tasks:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Tasks();
        $entity->setTaskscreatedby($this->getUser());

        $form = $this->createForm(new TasksType(), $entity);


        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $ordre = $em->getRepository('FullSixProjectForecastBundle:Tasks')->findMaxOrdreByProjectId($entity->getProjectsidprojects()->getId())+1;
            $entity->setTasksorder($ordre);
            $dates = $em->getRepository('FullSixProjectForecastBundle:Projects')->findDatesByProjetcTasks($entity->getProjectsidprojects()->getId());
            if ($dates['maxDate'] == '') {
                $entity->getProjectsidprojects()->setProjectfirsttasktdate($entity->getTasksbeginat());
                $entity->getProjectsidprojects()->setProjectlasttasktdate($entity->getTasksendat());
            } else {
                if ($entity->getTasksbeginat() < $minDate = new \DateTime($dates['minDate'])) {
                    $entity->getProjectsidprojects()->setProjectfirsttasktdate($entity->getTasksbeginat());
                } else {
                    $entity->getProjectsidprojects()->setProjectfirsttasktdate($minDate);
                }
                if ($entity->getTasksendat() > $maxDate = new \DateTime($dates['maxDate'])) {
                    $entity->getProjectsidprojects()->setProjectlasttasktdate($entity->getTasksendat());
                } else {
                    $entity->getProjectsidprojects()->setProjectlasttasktdate($maxDate);
                }
            }
            $entity->getProjectsidprojects()->setProjectsupdated();

            $em->persist($entity);
            $em->flush();

            return $this->redirect($request->headers->get('referer'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Tasks entity.
     *
     * @Route("/{id}/edit", name="tasks_edit")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Tasks')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tasks entity.');
        }

        $editForm = $this->createForm(new TasksType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Tasks entity.
     *
     * @Route("/{id}/update", name="tasks_update")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:Tasks:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:Tasks')->find($id);

        if (!$entity)
            throw $this->createNotFoundException('Unable to find Tasks entity.');

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new TasksType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid())
        {
            $dates = $em->getRepository('FullSixProjectForecastBundle:Projects')->findDatesByProjetcTasks($entity->getProjectsidprojects()->getId());



            if ($entity->getTasksbeginat() < new \DateTime($dates['minDate'])) {
                $entity->getProjectsidprojects()->setProjectfirsttasktdate($entity->getTasksbeginat());
            }
            if ($entity->getTasksendat() > new \DateTime($dates['maxDate'])) {
                $entity->getProjectsidprojects()->setProjectlasttasktdate($entity->getTasksendat());
            }
            $entity->getProjectsidprojects()->setProjectsupdated();

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tasks_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Tasks entity.
     *
     * @Route("/{id}/delete", name="tasks_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FullSixProjectForecastBundle:Tasks')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Tasks entity.');
            }

            $em->remove($entity);
            $em->flush();

            $projects_entity = $em->getRepository('FullSixProjectForecastBundle:Projects')->find($entity->getProjectsidprojects()->getId());
            $dates = $em->getRepository('FullSixProjectForecastBundle:Projects')->findDatesByProjetcTasks($entity->getProjectsidprojects()->getId());
            if ($dates['maxDate'] == '') {
                $projects_entity->setProjectfirsttasktdate(null);
                $projects_entity->setProjectlasttasktdate(null);
            } else {
                $minDate = new \DateTime($dates['minDate']);
                $maxDate = new \DateTime($dates['maxDate']);
                $projects_entity->setProjectfirsttasktdate($minDate);
                $projects_entity->setProjectlasttasktdate($maxDate);
            }
            $em->persist($projects_entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('projects_details', array('id' => $projects_entity->getId())));
    }

    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

    /**
    * @Route("/{id}/activate/{page}", name="tasks_activation")
    * @Template
    */
    public function activationAction($id, $page = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:Tasks')->find($id);

        $err = $entity->checkActivation();
        if(!empty($err)){
            $data = $this->container->get('fullsix_projectforecast.translation_utils')->buildErrorHtml($err);
            return new JsonResponse(array('status' => false, 'html' => $data), 200);
        }

        $entity->performActivationAndSideEffects();

        $em->persist($entity);
        $em->flush();

        return new JsonResponse(array('status' => true), 200);
    }

}

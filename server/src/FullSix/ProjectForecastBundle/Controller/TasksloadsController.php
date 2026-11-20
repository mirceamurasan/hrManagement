<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FullSix\ProjectForecastBundle\Entity\Tasksloads;
use FullSix\ProjectForecastBundle\Entity\Tasks;
use FullSix\ProjectForecastBundle\Form\TasksloadsType;
use FullSix\ProjectForecastBundle\Form\TasksloadsFromProjectType;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Tasksloads controller.
 *
 * @Route("/tasksloads")
 */
class TasksloadsController extends Controller {

    /**
     * Lists all Tasksloads entities.
     *
     * @Route("/", name="tasksloads")
     * @Template()
     */
    public function indexAction() {
        $source = new Entity('FullSixProjectForecastBundle:Tasksloads');
        $grid = $this->get('grid');
        $grid->setSource($source);

        $grid->setActionsColumnSeparator("&nbsp;&nbsp;");
        $rowAction_show = new RowAction("book", "tasksloads_show", false, "_self", array("title" => "Show"));
        $rowAction_show->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_show);

        $rowAction_edit = new RowAction("edit", "tasksloads_edit", false, "_self", array("title" => "Edit"));
        $rowAction_edit->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_edit);

        return $grid->getGridResponse('FullSixProjectForecastBundle::grid.html.twig',
            array(
                'title' => 'Charges',
                'entity_new' => "tasksloads_new"
                )
            );
    }

    /**
     * Finds and displays a Tasksloads entity.
     *
     * @Route("/{id}/show", name="tasksloads_show")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Tasksloads')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tasksloads entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Tasksloads entity.
     *
     * @Route("/new", name="tasksloads_new")
     * @Template()
     */
    public function newAction() {
        $entity = new Tasksloads();
        $form = $this->createForm(new TasksloadsType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Tasksloads entity.
     *
     * @Route("/newfromproject", name="tasksloads_newfromproject")
     * @Template()
     */
    public function newfromprojectAction($id) {
        $entity = new Tasksloads();

        $em = $this->getDoctrine()->getManager();
        $tasks = $em->getRepository('FullSixProjectForecastBundle:Tasks')->find($id);

        $entity->setTasksidtasks($tasks);
        $form = $this->createForm(new TasksloadsFromProjectType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new Tasksloads entity.
     *
     * @Route("/newfromprojectresponsive", name="tasksloads_newfromproject_responsive")
     * @Template()
     */
    public function newfromproject_responsiveAction($task_id) {

        $entity = new Tasksloads();

        $em = $this->getDoctrine()->getManager();
        $tasks = $em->getRepository('FullSixProjectForecastBundle:Tasks')->find($task_id);
        $resourceProfilesFlat = $em->getRepository('FullSixProjectForecastBundle:ResourceProfileFlat')->findBy(array('projectid' => $tasks->getProjectsidprojects()));

        $workers = array();
        foreach ($resourceProfilesFlat as $resourceProfileFlat) {
            $workers[] = $resourceProfileFlat->getWorkerid();
        }

        $entity->setTasksidtasks($tasks);
        $form = $this->createForm(new TasksloadsFromProjectType($workers), $entity);

        return array(
            'task_id' => $task_id,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Tasksloads entity.
     *
     * @Route("/create/task", name="tasksloads_create_from_project")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:Tasksloads:new.html.twig")
     */
    public function createFromProjectAction( Request $request) {

        $entity = new Tasksloads();
        $entity->setTasksloadscreatedby($this->getUser());

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new TasksloadsType(), $entity);
        $form->bind($request);

        if ($form->isValid())
        {
            $entity->getTasksidtasks()->setTasksupdated();
//            $project->setProjectsupdated();

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();


            $response = new Response( json_encode( array('status' => 'success', 'render' =>  $this->renderView("FullSixProjectForecastBundle:Tasksloads:_item.html.twig", array(
                    'tasksload' => $entity,
                    'cycle' => 'cycle-not-defined'
                )))));
            $response->headers->set('Content-Type', 'application/json');
            return $response;

        }


        $response = new Response( json_encode( array('status' => 'error', 'render' =>  $this->renderView("FullSixProjectForecastBundle:Tasksloads:newfromproject_responsive.html.twig",
                        array(
                            'entity' => $entity,
                            'task_id' => $entity->getTasksidtasks()->getId(),
                            'form' => $form->createView(),
                        )))));
            $response->headers->set('Content-Type', 'application/json');
            return $response;

    }

    /**
     * Creates a new Tasksloads entity.
     *
     * @Route("/create", name="tasksloads_create")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:Tasksloads:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Tasksloads();
        $entity->setTasksloadscreatedby($this->getUser());

        $form = $this->createForm(new TasksloadsType(), $entity);
        $form->bind($request);

        if ($form->isValid())
        {
            $entity->getTasksidtasks()->setTasksupdated();
            $entity->getTasksidtasks()->getProjectsidprojects()->setProjectsupdated();

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            if($request->isXmlHttpRequest())
            {
                return $this->render("FullSixProjectForecastBundle:Tasksloads:_item.html.twig", array(
                    'tasksload' => $entity,
                    'cycle' => 'cycle-not-defined'
                ));
            }

            return $this->redirect($request->headers->get('referer'));
        }

        if($request->isXmlHttpRequest())
        {
            return $this->render("FullSixProjectForecastBundle:Tasksloads:newfromproject_responsive.html.twig",
                array(
            'entity' => $entity,
            'form' => $form->createView(),
        ));
        }


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Tasksloads entity.
     *
     * @Route("/{id}/edit", name="tasksloads_edit")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Tasksloads')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tasksloads entity.');
        }

        $editForm = $this->createForm(new TasksloadsType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Tasksloads entity.
     *
     * @Route("/{id}/update", name="tasksloads_update")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:Tasksloads:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Tasksloads')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Tasksloads entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new TasksloadsType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $entity->getTasksidtasks()->setTasksupdated();
            $entity->getTasksidtasks()->getProjectsidprojects()->setProjectsupdated();

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('projects_details', array('id' => $entity->getTasksidtasks()->getProjectsidprojects()->getId())));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Tasksloads entity.
     *
     * @Route("/{id}/delete", name="tasksloads_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FullSixProjectForecastBundle:Tasksloads')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Tasksloads entity.');
            }
            $em->remove($entity);
            $em->flush();

            $projects_entity = $em->getRepository('FullSixProjectForecastBundle:Projects')->find($entity->getTasksidtasks()->getProjectsidprojects()->getId());
            $projects_entity->setProjectsupdated();
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
    * @Route("/{id}/activate/{page}", name="taskloads_activation")
    * @Template
    */
    public function activationAction($id, $page = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:Tasksloads')->find($id);

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

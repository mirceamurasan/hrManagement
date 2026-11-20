<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FullSix\ProjectForecastBundle\Entity\Jobs;
use FullSix\ProjectForecastBundle\Form\JobsType;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Jobs controller.
 *
 * @Route("/administration/jobs")
 */
class JobsController extends Controller {

    /**
     * Lists all Jobs entities.
     *
     * @Route("/", name="jobs")
     *
     */
    public function indexAction() {
        $source = new Entity('FullSixProjectForecastBundle:Jobs');
        $grid = $this->get('grid');
        $grid->setSource($source);

        $grid->setActionsColumnSeparator("&nbsp;&nbsp;");
        $rowAction_show = new RowAction("book", "jobs_show", false, "_self",
            array("title"=> $this->get('translator')->trans('common.action.show')));
        $rowAction_show->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_show);

        $rowAction_edit = new RowAction("edit", "jobs_edit", false, "_self",
            array("title"=>$this->get('translator')->trans('common.action.edit')));
        $rowAction_edit->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_edit);

        $rowAction_active = new RowAction("play", "jobs_activation", false, "_self",
            array("title" => $this->get('translator')->trans('common.action.activate')));
        $rowAction_active->setRouteParameters(array('id'));
        $rowAction_active->manipulateRender(
            function ($action, $row)
            {
                if ($row->getField('isTrashed') == 0) {
                    $action->setTitle('pause');
                    $action->setAttributes(array("title" => "Pause"));
                }

                return $action;
            }
        );
        $grid->addRowAction($rowAction_active);

        return $grid->getGridResponse('FullSixProjectForecastBundle::grid.html.twig',
            array(
                'title' => $this->get('translator')->trans('admin.job.title'),
                'entity_new' => 'jobs_new'
                )
            );
    }

    /**
     * Finds and displays a Jobs entity.
     *
     * @Route("/{id}/show", name="jobs_show")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Jobs')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Jobs entity.');
        }

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Displays a form to create a new Jobs entity.
     *
     * @Route("/new", name="jobs_new")
     * @Template()
     */
    public function newAction() {
        $entity = new Jobs();
        $form = $this->createForm(new JobsType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Jobs entity.
     *
     * @Route("/create", name="jobs_create")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:Jobs:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Jobs();
        $form = $this->createForm(new JobsType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('jobs'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Jobs entity.
     *
     * @Route("/{id}/edit", name="jobs_edit")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Jobs')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Jobs entity.');
        }

        $editForm = $this->createForm(new JobsType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Jobs entity.
     *
     * @Route("/{id}/update", name="jobs_update")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:Jobs:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Jobs')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Jobs entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new JobsType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('jobs', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Jobs entity.
     *
     * @Route("/{id}/delete", name="jobs_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FullSixProjectForecastBundle:Jobs')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Jobs entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('jobs'));
    }

    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

    /**
    * @Route("/{id}/activate/{page}", name="jobs_activation")
    * @Template
    */
    public function activationAction($id, $page = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:Jobs')->find($id);

        $err = $entity->checkActivation();
        if(!empty($err)){
            $data = $this->container->get('fullsix_projectforecast.translation_utils')->buildErrorHtml($err);
            return new JsonResponse(array('status' => false, 'html' => $data), 200);
        }

        $entity->getIsTrashed();
        $entity->setIsTrashed(!$entity->getIsTrashed());

        $em->persist($entity);
        $em->flush();

        return new JsonResponse(array('status' => true), 200);
    }

}

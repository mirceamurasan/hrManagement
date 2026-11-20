<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FullSix\ProjectForecastBundle\Entity\ProjectStatuses;
use FullSix\ProjectForecastBundle\Form\ProjectStatusesType;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * ProjectStatuses controller.
 *
 * @Route("/administration/projectstatuses")
 */
class ProjectStatusesController extends Controller {

    /**
     * Lists all ProjectStatuses entities.
     *
     * @Route("/", name="projectstatuses")
     * @Template()
     */
    public function indexAction() {
        $source = new Entity('FullSixProjectForecastBundle:ProjectStatuses');
        $grid = $this->get('grid');
        $grid->setSource($source);

        $grid->setActionsColumnSeparator("&nbsp;&nbsp;");
        $rowAction_show = new RowAction("book", "projectstatuses_show", false, "_self", array("title"=>"Show"));
        $rowAction_show->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_show);

        $rowAction_edit = new RowAction("edit", "projectstatuses_edit", false, "_self", array("title"=>"Edit"));
        $rowAction_edit->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_edit);

        $rowAction_active = new RowAction("play", "projectstatuses_activation", false, "_self", array("title" => "Activate"));
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
                'title' => $this->get('translator')->trans('admin.status.title'),
                'entity_new' => "projectstatuses_new"
                )
            );
    }

    /**
     * Finds and displays a ProjectStatuses entity.
     *
     * @Route("/{id}/show", name="projectstatuses_show")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:ProjectStatuses')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProjectStatuses entity.');
        }

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Displays a form to create a new ProjectStatuses entity.
     *
     * @Route("/new", name="projectstatuses_new")
     * @Template()
     */
    public function newAction() {
        $entity = new ProjectStatuses();
        $form = $this->createForm(new ProjectStatusesType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new ProjectStatuses entity.
     *
     * @Route("/create", name="projectstatuses_create")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:ProjectStatuses:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new ProjectStatuses();
        $form = $this->createForm(new ProjectStatusesType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('projectstatuses'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ProjectStatuses entity.
     *
     * @Route("/{id}/edit", name="projectstatuses_edit")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:ProjectStatuses')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProjectStatuses entity.');
        }

        $editForm = $this->createForm(new ProjectStatusesType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing ProjectStatuses entity.
     *
     * @Route("/{id}/update", name="projectstatuses_update")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:ProjectStatuses:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:ProjectStatuses')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ProjectStatuses entity.');
        }

        $editForm = $this->createForm(new ProjectStatusesType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('projectstatuses', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a ProjectStatuses entity.
     *
     * @Route("/{id}/delete", name="projectstatuses_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FullSixProjectForecastBundle:ProjectStatuses')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ProjectStatuses entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('projectstatuses'));
    }

    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

    /**
    * @Route("/{id}/activate/{page}", name="projectstatuses_activation")
    * @Template
    */
    public function activationAction($id, $page = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:ProjectStatuses')->find($id);

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

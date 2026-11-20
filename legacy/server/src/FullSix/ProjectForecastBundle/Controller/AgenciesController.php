<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FullSix\ProjectForecastBundle\Entity\Agencies;
use FullSix\ProjectForecastBundle\Form\AgenciesType;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Agencies controller.
 *
 * @Route("/administration/agencies")
 */
class AgenciesController extends Controller {

    /**
     * Lists all Agencies entities.
     *
     * @Route("/", name="agencies")
     * @Template()
     */
    public function indexAction() {
        $source = new Entity('FullSixProjectForecastBundle:Agencies');
        $grid = $this->get('grid');
        $grid->setSource($source);

        $grid->setActionsColumnSeparator("&nbsp;&nbsp;");
        $rowAction_show = new RowAction("book", "agencies_show", false, "_self",
            array("title"=> $this->get('translator')->trans('common.action.show')));
        $rowAction_show->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_show);

        $rowAction_edit = new RowAction("edit", "agencies_edit", false, "_self",
            array("title"=>$this->get('translator')->trans('common.action.edit')));
        $rowAction_edit->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_edit);

        $rowAction_active = new RowAction("play", "agencies_activation", false, "_self",
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
                'title' => $this->get('translator')->trans('admin.agency.title'),
                'entity_new' => "agencies_new"
                )
            );
    }

    /**
     * Finds and displays a Agencies entity.
     *
     * @Route("/{id}/show", name="agencies_show")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Agencies')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Agencies entity.');
        }

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Displays a form to create a new Agencies entity.
     *
     * @Route("/new", name="agencies_new")
     * @Template()
     */
    public function newAction() {
        $entity = new Agencies();
        $form = $this->createForm(new AgenciesType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Agencies entity.
     *
     * @Route("/create", name="agencies_create")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:Agencies:new.html.twig")
     */
    public function createAction(Request $request) {
        $user = $this->getUser();

        $entity = new Agencies();
        $entity->setAgenciescreatedby($user);

        $form = $this->createForm(new AgenciesType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('agencies'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Agencies entity.
     *
     * @Route("/{id}/edit", name="agencies_edit")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Agencies')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Agencies entity.');
        }

        $editForm = $this->createForm(new AgenciesType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Agencies entity.
     *
     * @Route("/{id}/update", name="agencies_update")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:Agencies:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Agencies')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Agencies entity.');
        }

        $editForm = $this->createForm(new AgenciesType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('agencies', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Agencies entity.
     *
     * @Route("/{id}/delete", name="agencies_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FullSixProjectForecastBundle:Agencies')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Agencies entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('agencies'));
    }

    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

    /**
    * @Route("/{id}/activate/{page}", name="agencies_activation")
    * @Template
    */
    public function activationAction($id, $page = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:Agencies')->find($id);

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

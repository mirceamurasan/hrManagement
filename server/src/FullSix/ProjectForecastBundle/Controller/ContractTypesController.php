<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FullSix\ProjectForecastBundle\Entity\ContractTypes;
use FullSix\ProjectForecastBundle\Form\ContractTypesType;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * ContractTypes controller.
 *
 * @Route("/administration/contracttypes")
 */
class ContractTypesController extends Controller {

    /**
     * Lists all ContractTypes entities.
     *
     * @Route("/", name="contracttypes")
     *
     */
    public function indexAction() {
        $source = new Entity('FullSixProjectForecastBundle:ContractTypes');
        $grid = $this->get('grid');
        $grid->setSource($source);

        $grid->setActionsColumnSeparator("&nbsp;&nbsp;");
        $rowAction_show = new RowAction("book", "contracttypes_show", false, "_self",
            array("title"=>$this->get('translator')->trans('common.action.show')));
        $rowAction_show->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_show);

        $rowAction_edit = new RowAction("edit", "contracttypes_edit", false, "_self",
            array("title"=>$this->get('translator')->trans('common.action.edit')));
        $rowAction_edit->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_edit);

        $rowAction_active = new RowAction("play", "contracttypes_activation", false, "_self",
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
                'title' =>  $this->get('translator')->trans('admin.typeCon.title'),
                'entity_new' => 'contracttypes_new'
                )
            );
    }

    /**
     * Finds and displays a ContractTypes entity.
     *
     * @Route("/{id}/show", name="contracttypes_show")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:ContractTypes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContractTypes entity.');
        }

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Displays a form to create a new ContractTypes entity.
     *
     * @Route("/new", name="contracttypes_new")
     * @Template()
     */
    public function newAction() {
        $entity = new ContractTypes();
        $form = $this->createForm(new ContractTypesType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new ContractTypes entity.
     *
     * @Route("/create", name="contracttypes_create")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:ContractTypes:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new ContractTypes();
        $form = $this->createForm(new ContractTypesType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('contracttypes'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ContractTypes entity.
     *
     * @Route("/{id}/edit", name="contracttypes_edit")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:ContractTypes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContractTypes entity.');
        }

        $editForm = $this->createForm(new ContractTypesType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing ContractTypes entity.
     *
     * @Route("/{id}/update", name="contracttypes_update")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:ContractTypes:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:ContractTypes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ContractTypes entity.');
        }

        $editForm= $this->createForm(new ContractTypesType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('contracttypes', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a ContractTypes entity.
     *
     * @Route("/{id}/delete", name="contracttypes_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FullSixProjectForecastBundle:ContractTypes')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ContractTypes entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('contracttypes'));
    }

    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

    /**
    * @Route("/{id}/activate/{page}", name="contracttypes_activation")
    * @Template
    */
    public function activationAction($id, $page = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:ContractTypes')->find($id);

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

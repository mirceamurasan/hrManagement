<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FullSix\ProjectForecastBundle\Entity\DaysoffsGlobal;
use FullSix\ProjectForecastBundle\Form\DaysoffsGlobalType;
use APY\DataGridBundle\Grid\Source\Vector;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Column\DateColumn;
use APY\DataGridBundle\Grid\Action\RowAction;

/**
 * DaysoffsGlobal controller.
 *
 * @Route("/administration/daysoffsglobal")
 */
class DaysoffsGlobalController extends Controller
{
    /**
     * Lists all DaysoffsGlobal entities.
     *
     * @Route("/", name="daysoffsglobal")
     * @Template()
     */

    public function indexAction() {
        $source = new Entity('FullSixProjectForecastBundle:DaysoffsGlobal');
        $grid = $this->get('grid');
        $grid->setSource($source);

        $grid->setActionsColumnSeparator("&nbsp;&nbsp;");
        $rowAction_show = new RowAction("book", "daysoffsglobal_show", false, "_self", array("title" => "Show"));
        $rowAction_show->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_show);

        $rowAction_edit = new RowAction("edit", "daysoffsglobal_edit", false, "_self", array("title" => "Edit"));
        $rowAction_edit->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_edit);

        $rowAction_delete = new RowAction("trash", "daysoffsglobal_delete", true, "_self", array("title" => "Supprimer", 'class' => 'grid_delete_action'));
        $rowAction_delete->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_delete);

        return $grid->getGridResponse('FullSixProjectForecastBundle::grid.html.twig',
            array(
                'title' => 'Global days off',
                'entity_new' => "daysoffsglobal_new"
                )
            );
    }

    /**
     * Finds and displays a DaysoffsGlobal entity.
     *
     * @Route("/{id}/show", name="daysoffsglobal_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:DaysoffsGlobal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DaysoffsGlobal entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new DaysoffsGlobal entity.
     *
     * @Route("/new", name="daysoffsglobal_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new DaysoffsGlobal();
        $form   = $this->createForm(new DaysoffsGlobalType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new DaysoffsGlobal entity.
     *
     * @Route("/create", name="daysoffsglobal_create")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:DaysoffsGlobal:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new DaysoffsGlobal();
        $form = $this->createForm(new DaysoffsGlobalType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('daysoffsglobal'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing DaysoffsGlobal entity.
     *
     * @Route("/{id}/edit", name="daysoffsglobal_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:DaysoffsGlobal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DaysoffsGlobal entity.');
        }

        $editForm = $this->createForm(new DaysoffsGlobalType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing DaysoffsGlobal entity.
     *
     * @Route("/{id}/update", name="daysoffsglobal_update")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:DaysoffsGlobal:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:DaysoffsGlobal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DaysoffsGlobal entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new DaysoffsGlobalType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('daysoffsglobal', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a DaysoffsGlobal entity.
     *
     * @Route("/{id}/delete", name="daysoffsglobal_delete")
     * @Method({"POST", "GET"})
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:DaysoffsGlobal')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DaysoffsGlobal entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('daysoffsglobal'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}

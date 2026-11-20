<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FullSix\ProjectForecastBundle\Entity\Providers;
use FullSix\ProjectForecastBundle\Form\ProvidersType;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;

/**
 * Providers controller.
 *
 * @Route("/providers")
 */
class ProvidersController extends Controller {

    /**
     * Lists all Providers entities.
     *
     * @Route("/", name="providers")
     * @Template()
     */
    public function indexAction() {
        $source = new Entity('FullSixProjectForecastBundle:Providers');
        $grid = $this->get('grid');
        $grid->setSource($source);

        $grid->setActionsColumnSeparator("&nbsp;&nbsp;");
        $rowAction_show = new RowAction("book", "providers_show", false, "_self",
            array("title" => $this->get('translator')->trans('common.action.show')));
        $rowAction_show->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_show);

        if ($this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            $rowAction_edit = new RowAction("edit", "providers_edit", false, "_self",
                array("title" => $this->get('translator')->trans('common.action.edit')));
            $rowAction_edit->setRouteParameters(array('id'));
            $grid->addRowAction($rowAction_edit);

            $rowAction_active = new RowAction("play", "providers_activation", false, "_self",
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
        }

        return $grid->getGridResponse('FullSixProjectForecastBundle::grid.html.twig',
            array(
                'title' => $this->get('translator')->trans('provider.title'),
                'entity_new' => "providers_new"
                )
            );
    }

    /**
     * Finds and displays a Providers entity.
     *
     * @Route("/{id}/show", name="providers_show")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Providers')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Providers entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Providers entity.
     *
     * @Route("/new", name="providers_new")
     * @Template()
     */
    public function newAction() {
        $entity = new Providers();
        $form = $this->createForm(new ProvidersType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Providers entity.
     *
     * @Route("/create", name="providers_create")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:Providers:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Providers();
        $form = $this->createForm(new ProvidersType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('providers'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Providers entity.
     *
     * @Route("/{id}/edit", name="providers_edit")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Providers')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Providers entity.');
        }

        $editForm = $this->createForm(new ProvidersType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Providers entity.
     *
     * @Route("/{id}/update", name="providers_update")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:Providers:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Providers')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Providers entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ProvidersType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('providers_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Providers entity.
     *
     * @Route("/{id}/delete", name="providers_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FullSixProjectForecastBundle:Providers')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Providers entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('providers'));
    }

    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

    /**
    * @Route("/{id}/activate/{page}", name="providers_activation")
    * @Template
    */
    public function activationAction($id, $page = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:Providers')->find($id);

        $entity->getIsTrashed();
        $entity->setIsTrashed(!$entity->getIsTrashed());

        $em->persist($entity);
        $em->flush();

        if ($page == "edit") {
            return $this->redirect($this->generateUrl('providers_edit', array('id' => $id)));
        } elseif ($page == "show") {
            return $this->redirect($this->generateUrl('providers_show', array('id' => $id)));
        }

        return $this->redirect($this->generateUrl('providers'));
    }

}

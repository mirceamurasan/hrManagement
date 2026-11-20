<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FullSix\ProjectForecastBundle\Entity\Employers;
use FullSix\ProjectForecastBundle\Form\EmployersType;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Employers controller.
 *
 * @Route("/administration/employers")
 */
class EmployersController extends Controller {

    /**
     * Lists all Employers entities.
     *
     * @Route("/", name="employers")
     * @Template()
     */
    public function indexAction() {
        $source = new Entity('FullSixProjectForecastBundle:Employers');
        $grid = $this->get('grid');
        $grid->setSource($source);

        $grid->setActionsColumnSeparator("&nbsp;&nbsp;");
        $rowAction_show = new RowAction("book", "employers_show", false, "_self",
            array("title" => $this->get('translator')->trans('common.action.show')));
        $rowAction_show->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_show);

        $rowAction_edit = new RowAction("edit", "employers_edit", false, "_self",
            array("title" => $this->get('translator')->trans('common.action.edit')));
        $rowAction_edit->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_edit);

        $rowAction_active = new RowAction("play", "employers_activation", false, "_self",
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
                'title' => $this->get('translator')->trans('admin.employer.title'),
                'entity_new' => "employers_new"
                )
            );
    }

    /**
     * Finds and displays a Employers entity.
     *
     * @Route("/{id}/show", name="employers_show")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Employers')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Employers entity.');
        }

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Displays a form to create a new Employers entity.
     *
     * @Route("/new", name="employers_new")
     * @Template()
     */
    public function newAction() {
        $entity = new Employers();
        $form = $this->createForm(new EmployersType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Employers entity.
     *
     * @Route("/create", name="employers_create")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:Employers:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Employers();
        $form = $this->createForm(new EmployersType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('employers'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Employers entity.
     *
     * @Route("/{id}/edit", name="employers_edit")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Employers')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Employers entity.');
        }

        $editForm = $this->createForm(new EmployersType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Employers entity.
     *
     * @Route("/{id}/update", name="employers_update")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:Employers:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Employers')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Employers entity.');
        }

        $editForm = $this->createForm(new EmployersType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $this->container->get('fullsix_projectforecast.denormalisation_helper')->updateEmployerDenormalisation($entity);

            return $this->redirect($this->generateUrl('employers'));
        }

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Employers entity.
     *
     * @Route("/{id}/delete", name="employers_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FullSixProjectForecastBundle:Employers')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Employers entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('employers'));
    }

    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

    /**
    * @Route("/{id}/activate/{page}", name="employers_activation")
    * @Template
    */
    public function activationAction($id, $page = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:Employers')->find($id);

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

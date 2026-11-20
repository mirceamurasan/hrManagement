<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FullSix\ProjectForecastBundle\Entity\Locations;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use FullSix\ProjectForecastBundle\Form\LocationsType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Locations controller.
 *
 * @Route("/administration/locations")
 */

class LocationsController extends Controller {

    /**
     * Lists all Locations entities.
     *
     * @Route("/", name="locations")
     * @Template()
     */

    public function indexAction()
    {
        $source = new Entity('FullSixProjectForecastBundle:Locations');
        $grid = $this->get('grid');
        $grid->setSource($source);
        $grid->setActionsColumnSeparator("&nbsp;&nbsp;");
        $rowAction_show = new RowAction("book", "locations_show", false, "_self", array("title" => "Afficher"));
        $rowAction_show->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_show);

        $rowAction_edit = new RowAction("edit", "locations_edit", false, "_self", array("title" => "Editer"));
        $rowAction_edit->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_edit);


        $rowAction_active = new RowAction("play", "locations_activation", false, "_self", array("title" => "Activate"));
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
                'title' => 'Locations',
                'entity_new' => "locations_new"
                )
            );
    }

    /**
     * Finds and displays a Locations entity.
     *
     * @Route("/{id}/show", name="locations_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Locations')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Locations entity.');
        }

        return array(
            'entity'      => $entity,
        );
    }

    /**
     * Displays a form to create a new Locations entity.
     *
     * @Route("/new", name="locations_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Locations();
        $form   = $this->createForm(new LocationsType(), $entity);


        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Locations entity.
     *
     * @Route("/create", name="locations_create")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:Locations:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Locations();
        $form = $this->createForm(new LocationsType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('locations'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Locations entity.
     *
     * @Route("/{id}/edit", name="locations_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Locations')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Locations entity.');
        }

        $editForm = $this->createForm(new LocationsType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Locations entity.
     *
     * @Route("/{id}/update", name="locations_update")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:Locations:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Locations')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Locations entity.');
        }

        $editForm = $this->createForm(new LocationsType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('locations', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        );
    }

    /**
     * Deletes a Locations entity.
     *
     * @Route("/{id}/delete", name="locations_delete")
     * @Method({"POST", "GET"})
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:Locations')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Locations entity.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('locations'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    /**
    * @Route("/{id}/activate/{page}", name="locations_activation")
    * @Template
    */
    public function activationAction($id, $page = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:Locations')->find($id);

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

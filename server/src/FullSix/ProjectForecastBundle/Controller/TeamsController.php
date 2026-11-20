<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use FullSix\ProjectForecastBundle\Form\TeamType;
use FullSix\ProjectForecastBundle\Entity\Teams;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Teams controller.
 *
 *
 */
class TeamsController extends Controller
{
    /**
     * Lists all Teams entities.
     *
     * @Route("/teams/", name="teams")
     * @Template()
     */
    public function indexAction()
    {

        $source = new Entity('FullSixProjectForecastBundle:Teams');
        $grid = $this->get('grid');
        $grid->setSource($source);

        $grid->setActionsColumnSeparator("&nbsp;&nbsp;");
        $rowAction_show = new RowAction("book", "teams_show", false, "_self", array("title" => "Show"));
        $rowAction_show->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_show);

        if ($this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            $rowAction_edit = new RowAction("edit", "teams_edit", false, "_self", array("title" => "Edit"));
            $rowAction_edit->setRouteParameters(array('id'));
            $grid->addRowAction($rowAction_edit);

            $rowAction_active = new RowAction("play", "teams_activation", false, "_self", array("title" => "Activate"));
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
                'title' => $this->get('translator')->trans('team.label'),
                'entity_new' => "teams_new"
                )
            );
    }
    /**
     * Displays a form to create a new Teams entity.
     *
     * @Route("/teams/new", name="teams_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Teams();
        $form   = $this->createForm(new TeamType(), $entity);

        $request = $this->getRequest();

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('teams'));
            }
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Teams entity.
     *
     * @Route("/teams/{id}/show", name="teams_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Teams')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Users entity.');
        }

        return array(
            'entity'      => $entity,
        );
    }

    /**
     * Displays a form to edit an existing Teams entity.
     *
     * @Route("/teams/{id}/edit", name="teams_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Teams')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Users entity.');
        }

        $source = new Entity('FullSixProjectForecastBundle:Workers');
        $source->manipulateQuery(
        function ($query) use ($id)
            {
                $query->andWhere('_team.id = '. $id);
            }
        );
        $grid = $this->get('grid');
        $grid->setSource($source);
        $source->manipulateRow(function ($row) {
            $row->setField('user.usersfirstname', $row->getField('user.usersfirstname').' '.$row->getField('user.userslastname'));


            $endDate = $row->getField('endworkingdate');
            $startDate = $row->getField('startworkingdate');
            $today = new \DateTime();
            if (!empty($startDate) && $startDate <= $today
                && (empty($endDate) || $endDate >= $today)) {
                $row->setField('active', "Yes");
            } else {
                $row->setField('active', "No");
            }
            return $row;
        });

        $grid->setActionsColumnSeparator("&nbsp;&nbsp;");
        $rowAction_delete = new RowAction("trash", "remove_team_worker", true, "_self", array("title" => "Supprimer", 'class' => 'grid_delete_action'));
        $rowAction_delete->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_delete);

        $grid->getGridResponse('FullSixProjectForecastBundle::grid.html.twig',
            array(
                'title' => $this->get('translator')->trans('worker.workers'),
                'entity_new' => "workers_new"
                )
            );

        $editForm = $this->createForm(new TeamType(), $entity);
        $request = $this->getRequest();

        if ($request->isMethod('POST')) {
            $editForm->bind($request);
            if ($editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('teams'));
            }
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'grid' => $grid
        );
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    /**
     * @Route("/{id}/activate/{page}", name="teams_activation")
     * @Template
     */
    public function activationAction($id, $page = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:Teams')->find($id);

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

    /**
     * @Route("/{id}/remove-worker/", name="remove_team_worker")
     * @Template
     */
    public function removeWorkerAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:Workers')->find($id);

        $entity->setTeam(NULL);
        $em->persist($entity);
        $em->flush();

        $referer = $this->getRequest()->headers->get('referer');
        return $this->redirect($referer);
    }
}
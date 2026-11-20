<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use FullSix\ProjectForecastBundle\Entity\Workers;
use FullSix\ProjectForecastBundle\Entity\Users;
use FullSix\ProjectForecastBundle\Form\WorkersType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
/**
 * Workers controller.
 *
 *
 */
class WorkersController extends Controller
{

    /*
    -------------------------------------------
                < ADMINISTRATION >
    -------------------------------------------
    */
    /**
     * Lists all Workers entities.
     *
     * @Route("/workers/", name="workers")
     * @Template()
     */
    public function indexAction()
    {
        $source = new Entity('FullSixProjectForecastBundle:Workers');
        $grid = $this->get('grid');
        $grid->setSource($source);

        $grid->setActionsColumnSeparator("&nbsp;&nbsp;");
        $rowAction_show = new RowAction("book", "workers_show", false, "_self", array("title" => "Show"));
        $rowAction_show->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_show);

        if ($this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            $rowAction_edit = new RowAction("edit", "workers_edit", false, "_self", array("title" => "Edit"));
            $rowAction_edit->setRouteParameters(array('id'));
            $grid->addRowAction($rowAction_edit);
        }


        $source->manipulateRow(
            function ($row) {
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
            }
        );


        return $grid->getGridResponse(
            'FullSixProjectForecastBundle::grid.html.twig',
            array(
                'title' => $this->get('translator')->trans('worker.workers'),
                'entity_new' => "workers_new"
            )
        );
    }

    /**
     * Displays a form to create a new Workers entity.
     *
     * @Route("/workers/new", name="workers_new")
     * @Template()
     */
    public function newAction()
    {
        
//die("workersCOn:new");
        $entity = new Workers();
        $em = $this->getDoctrine()->getManager();
        $roles = $em->getRepository('FullSixProjectForecastBundle:Roles')->getRolesAsArray(false);
        $form = $this->createForm(new WorkersType($roles), $entity);

        $request = $this->getRequest();

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {

                if ($entity->getUser()->getLogin()->getUsername() == null) {
                    $entity->getUser()->setLogin(null);
                } else {
                    $user = $entity->getUser();
                    $user->getLogin()->setEmail($user->getEmail());
                    $user->getLogin()->setEnabled(true);
                    $em->persist($user);
                }

                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('workers'));
            }
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Users entity.
     *
     * @Route("/workers/{id}/show", name="workers_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Workers')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Users entity.');
        }

        $message = '';
        $user = $entity->getUser();
        if($user->getLogin()){
            if ($user->getLogin()->isDirty()) {
                $message = 'user.warning.is_dirty';
            }else {
                $alternateLogin = $em->getRepository('FullSixProjectForecastBundle:Login')->findOneBy(array('username' => $user->getLogin()->getUsername(), 'dirty' => true));
                if ($alternateLogin instanceof UserInterface) {
                    $message = 'user.warning.existsDirty';
                }
            }
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity->getUser(),
            'worker' => $entity,
            'delete_form' => $deleteForm->createView(),
            'message' => $message
        );
    }

    /**
     * Displays a form to edit an existing Workers entity.
     *
     * @Route("/workers/{id}/edit", name="workers_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Workers')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Users entity.');
        }

        $requiredLogin = (false || $entity->getUser()->getLogin());
        $showPass = (false || !$entity->getUser()->getLogin());
        $user = $entity->getUser();
        $readonly = ($user->getLogin() && $user->isAdLinked());
        $roles = $em->getRepository('FullSixProjectForecastBundle:Roles')->getRolesAsArray(false);
        $editForm = $this->createForm(new WorkersType($roles, $requiredLogin, $showPass, $readonly), $entity);

        $contractsGrid = $this->getContractsGrid($id);
        $vacationsGrid = $this->getVacationsGrid($id);

        $request = $this->getRequest();

        if ($request->isMethod('POST')) {
            $editForm->bind($request);

            if ($editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();

                if ($user->getLogin()->getUsername() == null) {
                    $user->setLogin(null);

                } else {
                    $user->getLogin()->setEmail($user->getEmail());
                    $this->container->get(
                        'fullsix_projectforecast.resource_profile_helper'
                    )->updateRolesOnResourceProfilesByWorkers($entity);
                    $em->persist($user);
                }

                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('workers_edit', array('id' => $id)));
            }
        }

        $message = '';
        $user = $entity->getUser();
        if($user->getLogin()){
            if ($user->getLogin()->isDirty()) {
                $message = 'user.warning.is_dirty';
            }else {
                $alternateLogin = $em->getRepository('FullSixProjectForecastBundle:Login')->findOneBy(array('username' => $user->getLogin()->getUsername(), 'dirty' => true));
                if ($alternateLogin instanceof UserInterface) {
                    $message = 'user.warning.existsDirty';
                }
            }
        }

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'contractsGrid' => $contractsGrid,
            'vacationsGrid' => $vacationsGrid,
            'message'   => $message
        );
    }

    private function getContractsGrid($id)
    {

        $source = new Entity('FullSixProjectForecastBundle:Contracts');
        $source->manipulateQuery(
            function ($query) use ($id) {
                $query->andWhere('_idworker.id = ' . $id);
            }
        );
        $grid = $this->get('grid');
        $grid->setSource($source);

        $grid->setActionsColumnSeparator("&nbsp;&nbsp;");
        $rowAction_show = new RowAction("book", "contracts_show", false, "_self", array("title" => "Show"));
        $rowAction_show->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_show);

        $rowAction_edit = new RowAction("edit", "contracts_edit", false, "_self", array("title" => "Edit"));
        $rowAction_edit->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_edit);

        $rowAction_active = new RowAction("play", "contracts_activation", false, "_self", array("title" => "Activate"));
        $rowAction_active->setRouteParameters(array('id'));
        $rowAction_active->manipulateRender(
            function ($action, $row) {
                if ($row->getField('isTrashed') == 0) {
                    $action->setTitle('pause');
                    $action->setAttributes(array("title" => "Pause"));
                }

                return $action;
            }
        );
        $grid->addRowAction($rowAction_active);

        $grid->getGridResponse('FullSixProjectForecastBundle::grid.html.twig');

        return $grid;
    }

    private function getVacationsGrid($id)
    {
        $source = new Entity('FullSixProjectForecastBundle:Vacations');
         $source->manipulateQuery(
         function ($query) use ($id)
             {
                 $query->andWhere('_vacationperiods.idcontract IS NOT NULL ');
                 $query->andWhere('_vacationperiods_idcontract_idworker.id = '. $id);
             }
         );
        $grid = $this->get('grid');
        $grid->setSource($source);

        $grid->setActionsColumnSeparator("&nbsp;&nbsp;");
        $rowAction_show = new RowAction("book", "vacations_show", false, "_self", array(
            "title" => "Afficher",
            'class' => 'vacation-popup'
        ));
        $rowAction_show->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_show);

        $rowAction_edit = new RowAction("edit", "vacations_edit", false, "_self", array(
            "title" => "Edit",
            'class' => 'vacation-popup'
        ));
        $rowAction_edit->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_edit);

        $rowAction_active = new RowAction("play", "vacations_activation", false, "_self", array("title" => "Activate"));
        $rowAction_active->setRouteParameters(array('id'));
        $rowAction_active->manipulateRender(
            function ($action, $row) {
                if ($row->getField('isTrashed') == 0) {
                    $action->setTitle('pause');
                    $action->setAttributes(array("title" => "Pause"));
                }

                return $action;
            }
        );
        $grid->addRowAction($rowAction_active);

        $grid->getGridResponse('FullSixProjectForecastBundle::grid.html.twig');

        return $grid;
    }

    /**
     * Displays a form to hire an User
     *
     * @Route("/workers/{id}/hire", name="hire_worker")
     * @Template()
     */
    public function hireAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('FullSixProjectForecastBundle:Users')->find($id);
        $entity = new Workers();
        $entity->setUser($user);

        $requiredLogin = (false || $user->getLogin());
        $showPass = (false || !$user->getLogin());
        $readonly = ($user->getLogin() && $user->isAdLinked());
        $roles = $em->getRepository('FullSixProjectForecastBundle:Roles')->getRolesAsArray(false);
        $form = $this->createForm(new WorkersType($roles, $requiredLogin, $showPass, $readonly), $entity);

        $request = $this->getRequest();

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $user = $entity->getUser();
                $em->persist($user);
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('workers'));
            }
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );

    }

    /*
    -------------------------------------------
                </ ADMINISTRATION >
    -------------------------------------------
    */

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm();
    }

    /**
     * Displays a form to hire an User
     *
     * @Route("/workers/get-team-location/", name="get_team_location")
     */
    public function getTeamLocationAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $id = $request->get('id');
            $team = $em->getRepository('FullSixProjectForecastBundle:Teams')->find($id);
            $location = $team->getTeamslocation();

            return new JsonResponse(array('status' => "OK", 'id' => $location->getId()));
        } else {
            throw $this->createNotFoundException();
        }
    }

}

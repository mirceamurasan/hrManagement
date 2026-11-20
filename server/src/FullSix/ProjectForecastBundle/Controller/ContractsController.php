<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FullSix\ProjectForecastBundle\Entity\Contracts;
use FullSix\ProjectForecastBundle\Entity\Workers;
use FullSix\ProjectForecastBundle\Entity\Periods;
use FullSix\ProjectForecastBundle\Form\ContractsType;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Contracts controller.
 *
 * @Route("/contracts")
 */
class ContractsController extends Controller {

    /**
     * Lists all Contracts entities.
     *
     * @Route("/", name="contracts")
     * @Template()
     */
    public function indexAction()
    {
        $source = new Entity('FullSixProjectForecastBundle:Contracts');
        $grid = $this->get('grid');
        $grid->setSource($source);

        $grid->setActionsColumnSeparator("&nbsp;&nbsp;");
        $rowAction_show = new RowAction("book", "contracts_show", false, "_self", array("title" => "Show"));
        $rowAction_show->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_show);

        /*$rowAction_edit = new RowAction("edit", "contracts_edit", false, "_self", array("title" => "Edit"));
        $rowAction_edit->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_edit);*/

        return $grid->getGridResponse('FullSixProjectForecastBundle::grid.html.twig',
            array(
                'title' => 'Contrats Prestataires',
                'entity_new' => "contracts_new"
                )
            );
    }

    /**
     * Finds and displays a Contracts entity.
     *
     * @Route("/{id}/show", name="contracts_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Contracts')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contracts entity.');
        }

        return array(
            'entity' => $entity,
        );
    }

    /**
     * Displays a form to create a new Contracts entity.
     *
     * @Route("/{id}/new", name="contracts_new")
     * @Template()
     */
    public function newAction($id)
    {

        $em = $this->getDoctrine()->getManager();
        $entity = new Contracts();
        $worker = $em->getRepository('FullSixProjectForecastBundle:Workers')->find($id);
        $entity->setIdworker($worker);

        $form = $this->createForm(new ContractsType(true), $entity);
        $form->get('workerfirstname')->setData($worker->getUser()->getUsersfirstname());
        $form->get('workerlastname')->setData($worker->getUser()->getUserslastname());

        $request = $this->getRequest();
        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                foreach ($entity->getPeriods() as $period) {
                    $period->setIdContract($entity) ;
                }

                //revalidate form : now periods objects are linked to contract
                $form = $this->createForm(new ContractsType(true), $entity);
                $form->bind($request); //mandatory
                if ($form->isValid()) {

                    $em->persist($entity);
                    $em->flush();

                    $this->container->get('fullsix_projectforecast.denormalisation_helper')->crudContractDenormalisation($entity);

                    return $this->redirect($this->generateUrl('workers_edit', array('id' => $id)));
                }
            }
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Contracts entity.
     *
     * @Route("/{id}/edit", name="contracts_edit")
     * @Template("FullSixProjectForecastBundle:Contracts:edit.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Contracts')->find($id);

        $grid = $this->getPeriodsGrid($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contracts entity.');
        }

        $editForm = $this->createForm(new ContractsType(), $entity);
        $editForm->get('workerfirstname')->setData($entity->getIdworker()->getUser()->getUsersfirstname());
        $editForm->get('workerlastname')->setData($entity->getIdworker()->getUser()->getUserslastname());
        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'grid' => $grid
        );
    }

    /**
     * Edits an existing Contracts entity.
     *
     * @Route("/{id}/update", name="contracts_update")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:Contracts:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Contracts')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contracts entity.');
        }

        $editForm = $this->createForm(new ContractsType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $this->container->get('fullsix_projectforecast.denormalisation_helper')->crudContractDenormalisation($entity);

            return $this->redirect($this->generateUrl('workers_edit', array('id' => $entity->getIdworker()->getId())));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    private function getPeriodsGrid($id) {

        $source = new Entity('FullSixProjectForecastBundle:Periods');
        $source->manipulateQuery(
        function ($query) use ($id)
            {
                $query->andWhere('_idcontract.id = '. $id);
            }
        );
        $grid = $this->get('grid');
        $grid->setSource($source);

        $grid->setActionsColumnSeparator("&nbsp;&nbsp;");
        $rowAction_show = new RowAction("book", "periods_show", false, "_self", array("title" => "Show", 'class' => 'period-popup'));
        $rowAction_show->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_show);

        $rowAction_edit = new RowAction("edit", "periods_edit", false, "_self", array("title" => "Edit", 'class' => 'period-popup'));
        $rowAction_edit->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_edit);

        $rowAction_active = new RowAction("play", "periods_activation", false, "_self", array("title" => "Activate"));
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

        $grid->getGridResponse('FullSixProjectForecastBundle::grid.html.twig');

        return $grid;
    }

    /**
     * Autocomplete lastname.
     *
     * @Route("/search.html", name="contracts_search")
     * @Method("POST")
     */
    public function searchAction(Request $request)
    {
		$em = $this->getDoctrine()->getManager();

		$names = $entity = $em->getRepository('FullSixProjectForecastBundle:Workers')->getByLikeLastname($request->get('lastname'));

		$response = new Response(json_encode(array('names' => $names)));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}

    /**
     * @Route("/{id}/activate/{page}", name="contracts_activation")
     * @Template
     */
    public function activationAction($id, $page = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:Contracts')->find($id);

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
     * Displays a form to hire an User
     *
     * @Route("/{id}/get_typeCon_external", name="get_typeCon_external")
     */
    public function getContractTypeExternalValue(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $id = $request->get('typeConId');

            if($id){
                $typeConExternal = $em->getRepository('FullSixProjectForecastBundle:ContractTypes')->find($id)->getContractTypesIsExternal();
            }else{
                $typeConExternal = false;
            }
            return new JsonResponse(array('status' => "OK", 'external' => $typeConExternal));
        } else {
            throw $this->createNotFoundException();
        }
    }
}

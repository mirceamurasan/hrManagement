<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FullSix\ProjectForecastBundle\Entity\Customers;
use FullSix\ProjectForecastBundle\Form\CustomersType;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use Symfony\Component\Security\Acl\Exception\Exception;
use FullSix\ProjectForecastBundle\Entity\ResourceProfile;
use FullSix\ProjectForecastBundle\Entity\ResourceProfileFlat;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * Customers controller.
 *
 * @Route("/customers")
 */
class CustomersController extends Controller {

    /**
     * Lists all Customers entities.
     *
     * @Route("/", name="customers")
     * @Template()
     */
    public function indexAction() {
        $source = new Entity('FullSixProjectForecastBundle:Customers');
        $grid = $this->get('grid');

        $currentLogin = $this->getUser();
        $workerId = $currentLogin->getUser()->getWorker();

        // if user is not admin, show only clients he is involved
        if (!$this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            $source->manipulateQuery(
                function (QueryBuilder $query) use ($workerId) {
                    $query->join('_a.resourceprofiles', 'rp');
                    $query->andWhere("rp.workerid = :workerid");
                    $query->setParameter('workerid', $workerId);
                }
            );
        }

        $grid->setSource($source);

        $grid->setDefaultFilters(array(
                'mergedWith.customersname' => array('operator' => 'isNull') ));

        $grid->setActionsColumnSeparator("&nbsp;&nbsp;");
        $rowAction_show = new RowAction("book", "customers_show", false, "_self", array("title"=>"Show"));
        $rowAction_show->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_show);

        if ($this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            $rowAction_edit = new RowAction("edit", "customers_edit", false, "_self", array("title"=>"Edit"));
            $rowAction_edit->setRouteParameters(array('id'));
            $rowAction_edit->manipulateRender(
                function ($action, $row)
                {
                    if ($row->getField('mergedWith.customersname') != '') {
                        return null;
                    }else{
                        return $action;
                    }
                }
            );
            $grid->addRowAction($rowAction_edit);

            $rowAction_active = new RowAction("play", "customers_activation", false, "_self",
                array("title" => $this->get('translator')->trans('common.action.activate')));
            $rowAction_active->setRouteParameters(array('id'));
            $rowAction_active->manipulateRender(
                function ($action, $row)
                {
                    if ($row->getField('mergedWith.customersname') != '') {
                        return null;
                    } elseif ($row->getField('isTrashed') == 0) {
                        $action->setTitle('pause');
                        $action->setAttributes(array("title" => "Pause"));
                    }

                    return $action;
                }
            );
            $grid->addRowAction($rowAction_active);
        }

        return $grid->getGridResponse('FullSixProjectForecastBundle::grid.html.twig', array('title' => 'Clients', 'entity_new' => 'customers_new'));
    }

    /**
     * Finds and displays a Customers entity.
     *
     * @Route("/{id}/show", name="customers_show")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Customers')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customers entity.');
        }

        if (!$this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            $workerId = $this->getUser()->getUser()->getWorker();
            $rp = $em->getRepository('FullSixProjectForecastBundle:ResourceProfile')->findOneBy(array(
                    'customerid' => $entity,
                    'workerid' => $workerId
                ));
            if(!$rp){
                throw new AccessDeniedException("The user has to be attached to the client");
            }
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Customers entity.
     *
     * @Route("/new", name="customers_new")
     * @Template()
     */
    public function newAction() {
        $entity = new Customers();
        $form = $this->createForm(new CustomersType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Customers entity.
     *
     * @Route("/create", name="customers_create")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:Customers:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Customers();

        $form = $this->createForm(new CustomersType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('customers'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Customers entity.
     *
     * @Route("/{id}/edit", name="customers_edit")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Customers')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customers entity.');
        }

        $projects = $entity->getProjects();

        $editForm = $this->createForm(new CustomersType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'projects' => $projects,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Customers entity.
     *
     * @Route("/{id}/update", name="customers_update")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:Customers:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Customers')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Customers entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CustomersType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('customers', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Customers entity.
     *
     * //@Route("/{id}/delete", name="customers_delete")
     * //@Method("POST")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FullSixProjectForecastBundle:Customers')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Customers entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('customers'));
    }

    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

    /**
    * @Route("/{id}/activate/{page}", name="customers_activation")
    * @Template
    */
    public function activationAction($id, $page = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:Customers')->find($id);

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
     * Popup.
     *
     * @Route("/merge-popup/{id}", name="merge_popup")
     */
    public function popupMergeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:Customers')->find($id);

        $customers = $em->getRepository('FullSixProjectForecastBundle:Customers')->getActiveClientsForMerge($id);

        return $this->render('FullSixProjectForecastBundle:Customers:mergePopup.html.twig', array(
            'entity' => $entity,
            'customers' => $customers,
        ));
    }

    /**
     * Merging Action
     *
     * @Route("/merge/{id}", name="merge")
     * @Method("POST");
     */
    public function mergeAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->get('merge') == "destination") {
            $mergedCustomer = $em->getRepository('FullSixProjectForecastBundle:Customers')->find($id);
            $masterCustomer = $em->getRepository('FullSixProjectForecastBundle:Customers')->find($request->get('customer'));
        } elseif ($request->get('merge') == "source") {
            $masterCustomer = $em->getRepository('FullSixProjectForecastBundle:Customers')->find($id);
            $mergedCustomer = $em->getRepository('FullSixProjectForecastBundle:Customers')->find($request->get('customer'));
        } else {
             throw new Exception('Please select the merge source and destination');
        }
        $mergedCustomer->setMergedWith($masterCustomer);
        $mergedCustomer->setIsTrashed(true);

        $existingResourceProfiles = $em->getRepository('FullSixProjectForecastBundle:ResourceProfile')->findBy(array('customerid' => $mergedCustomer));

        foreach ($existingResourceProfiles as $existingResourceProfile) {
            $this->container->get('fullsix_projectforecast.resource_profile_helper')->addConnectionBetweenCustomerAndWorker($masterCustomer->getId(), $existingResourceProfile->getWorkerid()->getId());
        }

        foreach($mergedCustomer->getSapids() as $mergedSapid) {
            $mergedSapid->setCustomers($masterCustomer);
        }

        foreach($mergedCustomer->getProjects() as $mergedProject) {
            $mergedProject->setCustomersidcustomers($masterCustomer);
        }

        $em->flush();

        return $this->redirect($this->generateUrl('customers'));
    }

    /**
     * Popup.
     *
     * @Route("/workers-popup/{id}", name="workers_popup")
     */
    public function popupWorkersAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $customer = $em->getRepository('FullSixProjectForecastBundle:Customers')->find($id);
        $workers = $em->getRepository('FullSixProjectForecastBundle:Workers')->findAll();

        return $this->render('FullSixProjectForecastBundle:Customers:workersPopup.html.twig', array(
            'workers' => $workers,
            'customer' => $customer,
        ));
    }

     /**
     * Add new worker to a client
     *
     * @Route("/add-worker/{id}", name="add_worker")
     * @Method("POST");
     */
    public function addWorkerAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $workerId = $request->get('worker');

        $this->container->get('fullsix_projectforecast.resource_profile_helper')->addConnectionBetweenCustomerAndWorker($id, $workerId);


        return $this->redirect($request->headers->get('referer'));

    }


     /**
     * Remove worker from a client
     *
     * @Route("/remove-worker/{customerId}/{workerId}", name="remove_worker")
     */
    public function removeWorkerAction($customerId, $workerId)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:ResourceProfile')->findOneBy(array(
            'customerid' => $customerId,
            'workerid' => $workerId
        ));

        $customer = $em->getRepository('FullSixProjectForecastBundle:Customers')->find($customerId);
        $customerProjects = $customer->getProjects();

        $projectIds = array();
        foreach ($customerProjects as $project) {
            $projectIds[] = $project->getId();
        }

        $removableResourceProfilesFlat = $em->getRepository('FullSixProjectForecastBundle:ResourceProfileFlat')->getProjectsAssignedToWorker($workerId, $projectIds);

        foreach ($removableResourceProfilesFlat as $removableResourceProfileFlat) {
            $em->remove($removableResourceProfileFlat);
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('customers_edit', array('id' => $customerId)));
    }

}

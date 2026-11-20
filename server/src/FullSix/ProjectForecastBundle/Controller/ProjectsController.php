<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FullSix\ProjectForecastBundle\Entity\Projects;
use FullSix\ProjectForecastBundle\Form\ProjectsType;
use FullSix\ProjectForecastBundle\Entity\Tasks;
use FullSix\ProjectForecastBundle\Entity\Tasksloads;
use FullSix\ProjectForecastBundle\Entity\Users;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use FullSix\ProjectForecastBundle\Entity\ResourceProfileFlat;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\ORM\EntityRepository;

/**
 * Projects controller.
 *
 * @Route("/projects")
 */
class ProjectsController extends Controller
{

    /**
     * Lists all Projects entities.
     *
     * @Route("/", name="projects")
     * @Template()
     */
    public function indexAction() {
        $source = new Entity('FullSixProjectForecastBundle:Projects');
        $grid = $this->get('grid');

        $currentLogin = $this->getUser();
        $workerId = $currentLogin->getUser()->getWorker();

        // if user is not admin, show only projects he is involved
        if (!in_array('ROLE_ADMIN', $currentLogin->getRoles()) && !in_array('ROLE_SUPER_ADMIN', $currentLogin->getRoles())) {
            $source->manipulateQuery(
                    function (QueryBuilder $query) use ($workerId) {
                        $query->join('_a.resourceprofilesflat', 'rp');
                        $query->andWhere("rp.workerid = :workerid");
                        $query->setParameter('workerid', $workerId);
                    }
            );
        }

        $grid->setSource($source);
        $grid->setDefaultFilters(array(
                'mergedWith.projectsname' => array('operator' => 'isNull') ));

        $grid->setActionsColumnSeparator("&nbsp;&nbsp;");
        $rowAction_show = new RowAction("book", "projects_details", false, "_self", array("title"=>"Show"));
        $rowAction_show->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_show);

        if ($this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            $rowAction_active = new RowAction("play", "projects_activation", false, "_self",
                array("title" => $this->get('translator')->trans('common.action.activate')));
            $rowAction_active->setRouteParameters(array('id'));
            $rowAction_active->manipulateRender(
                function ($action, $row)
                {
                    if ($row->getField('mergedWith.projectsname') != '') {
                        return null;
                    }elseif($row->getField('isTrashed') == 0) {
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
                'title' => 'Projects',
                'entity_new' => "projects_new"
                )
            );
    }

    /**
     * Finds and displays a Projects entity.
     *
     * @Route("/{id}/logs", name="projects_logs")
     * @Template()
     */
    public function showLogsAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Projects')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Projects entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Return details block at top.
     *
     * @Route("/{project_id}/details/block/detail", name="projects_block_details")
     * @Template("FullSixProjectForecastBundle:Projects:blocks/detailSlot.html.twig")
     */
    public function detailSlotAction($project_id)
    {
        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('FullSixProjectForecastBundle:Projects')->find($project_id);

        if (!$this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            $workerId = $this->getUser()->getUser()->getWorker();
            $rp = $em->getRepository('FullSixProjectForecastBundle:ResourceProfileFlat')->findOneBy(array(
                    'projectid' => $project,
                    'workerid' => $workerId
                ));
            if(!$rp){
                throw new AccessDeniedException("The user has to be attached to the project's client");
            }
        }

        $exit_interval = null;
        $exit_interval2 = null;
        if($project->getProjectfirsttasktdate() && $project->getProjectlasttasktdate())
        {
            $interval = date_diff($project->getProjectfirsttasktdate(), $project->getProjectlasttasktdate());
            $interval2 = date_diff($project->getProjectfirsttasktdate(), new \DateTime());
            $exit_interval = $interval->format('%a');
            $exit_interval2 = $interval2->format('%a');
        }

        return array(
            'project' => $project,
            'interval_date' => $exit_interval,
            'since_begining' => $exit_interval2,
        );
    }

    /**
     * Finds and displays a Projects entity with details.
     *
     * @Route("/{id}/details", name="projects_details")
     * @Template()
     */
    public function detailsAction($id) {
        $em = $this->getDoctrine()->getManager();

        $project = $em->getRepository('FullSixProjectForecastBundle:Projects')->find($id);
        if (!$project) {
            throw $this->createNotFoundException('Unable to find Projects entity.');
        }

        $tasks = $project->getProjectstasks();

      /*  foreach ($tasks as $task) {
           // $tasksloads = $em->getRepository('FullSixProjectForecastBundle:Tasksloads')->findNoTrashedByTaskId($task->getId());
         //   $task->tasksloads = $task->getTaskstasksloads();
        }*/

        $resourceProfilesFlat = $em->getRepository('FullSixProjectForecastBundle:ResourceProfileFlat')->findBy(
                array('projectid' => $id ),
                array('roles' => 'ASC')
                );

        $trashedForm = $this->createDeleteForm($id);

        return array(
            'project' => $project,
            'resourceprofiles' => $resourceProfilesFlat,
            'trashed_form' => $trashedForm->createView(),
//            'myProject' => $this->isMyProject($project),
        );
    }

    /**
     * Finds and displays a Projects entity.
     *
     * @Template()
     */
    public function logsAction($id) {
        $logs = array();
        $em = $this->getDoctrine();

        $log_repo = $em->getRepository('FullSixProjectForecastBundle:PfLogEntries');
        $projectEntity = $em->getRepository('FullSixProjectForecastBundle:Projects')->find($id);

        $log_project = $log_repo->getLogEntries($projectEntity);
        foreach ($log_project as $log) {
            $logId = $log->getLoggedAt()->getTimestamp().$log->getObjectClass().$log->getObjectId();
            $old_datas = $log->getOldData();
            if (is_array($old_datas)) {
                foreach ($old_datas as $valKey => $oldData) {
                    $new_datas = $log->getData();
                    if (is_array($new_datas[$valKey])) {
                        $logs[$logId][] = array(
                            'date' => $log->getLoggedAt()->format('d/m/Y H:i:s'),
                            'entityName' => 'Projects',
                            'action' => $log->getAction(),
                            'label' => $projectEntity->getProjectsname(),
                            'username' => $log->getUsername(),
                            'valKey' => $valKey,
                            'oldVal' => $oldData['id'],
                            'newVal' => $new_datas[$valKey]['id']
                        );
                    } else {
                        $logs[$logId][] = array(
                            'date' => $log->getLoggedAt()->format('d/m/Y H:i:s'),
                            'entityName' => 'Projects',
                            'action' => $log->getAction(),
                            'label' => $projectEntity->getProjectsname(),
                            'username' => $log->getUsername(),
                            'valKey' => $valKey,
                            'oldVal' => $oldData,
                            'newVal' => $new_datas[$valKey]
                        );
                    }
                }
            }
        }

        $tasksEntities = $projectEntity->getProjectstasks();
        foreach ($tasksEntities as $task) {
            $log_task = $log_repo->getLogEntries($task);
            foreach ($log_task as $log) {
                $logId = $log->getLoggedAt()->getTimestamp().$log->getObjectClass().$log->getObjectId();
                $old_datas = $log->getOldData();
                if (is_array($old_datas)) {
                    foreach ($old_datas as $valKey => $oldData) {
                        $new_datas = $log->getData();
                        if (is_array($new_datas[$valKey])) {
                            $logs[$logId][] = array(
                                'date' => $log->getLoggedAt()->format('d/m/Y H:i:s'),
                                'entityName' => 'Tasks',
                                'action' => $log->getAction(),
                                'label' => $task->getTasksname(),
                                'username' => $log->getUsername(),
                                'valKey' => $valKey,
                                'oldVal' => $oldData['id'],
                                'newVal' => $new_datas[$valKey]['id']
                            );
                        } else {
                            $logs[$logId][] = array(
                                'date' => $log->getLoggedAt()->format('d/m/Y H:i:s'),
                                'entityName' => 'Tasks',
                                'action' => $log->getAction(),
                                'label' => $task->getTasksname(),
                                'username' => $log->getUsername(),
                                'valKey' => $valKey,
                                'oldVal' => $oldData,
                                'newVal' => $new_datas[$valKey]
                            );
                        }
                    }
                }
            }

            $taskstasksloadsEntities = $task->getTaskstasksloads();
            foreach ($taskstasksloadsEntities as $taskstasksload) {
                $log_taskstasksload = $log_repo->getLogEntries($taskstasksload);
                foreach ($log_taskstasksload as $log) {
                    $logId = $log->getLoggedAt()->getTimestamp().$log->getObjectClass().$log->getObjectId();
                    $old_datas = $log->getOldData();
                    if (is_array($old_datas)) {
                        foreach ($old_datas as $valKey => $oldData) {
                            $new_datas = $log->getData();
                            if (is_array($new_datas[$valKey])) {
                                $logs[$logId][] = array(
                                    'date' => $log->getLoggedAt()->format('d/m/Y H:i:s'),
                                    'entityName' => 'Tasksloads',
                                    'action' => $log->getAction(),
                                    'label' => $taskstasksload->getTasksidtasks().'/'.$taskstasksload->getJobsidjobs(),
                                    'username' => $log->getUsername(),
                                    'valKey' => $valKey,
                                    'oldVal' => $oldData['id'],
                                    'newVal' => $new_datas[$valKey]['id']
                                );
                            } else {
                                $logs[$logId][] = array(
                                    'date' => $log->getLoggedAt()->format('d/m/Y H:i:s'),
                                    'entityName' => 'Tasksloads',
                                    'action' => $log->getAction(),
                                    'label' => $taskstasksload->getTasksidtasks().'/'.$taskstasksload->getJobsidjobs(),
                                    'username' => $log->getUsername(),
                                    'valKey' => $valKey,
                                    'oldVal' => $oldData,
                                    'newVal' => $new_datas[$valKey]
                                );
                            }
                        }
                    }
                }
            }
        }
        krsort($logs);

        return array('logs' => $logs);
    }

    /**
     * Displays a form to create a new Projects entity.
     *
     * @Route("/new", name="projects_new")
     * @Template()
     */
    public function newAction() {
        $entity = new Projects();
        $form = $this->createForm(new ProjectsType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Projects entity.
     *
     * @Route("/create", name="projects_create")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:Projects:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Projects();
        $entity->setProjectscreatedby($this->getUser());

        $form = $this->createForm(new ProjectsType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $resourceProfiles = $em->getRepository('FullSixProjectForecastBundle:ResourceProfile')->findBy(array('customerid' => $entity->getCustomer()));

            $em->persist($entity);
            $em->flush();

            /*  all customers's workers are being attached to the project */
            foreach ($resourceProfiles as $resourceProfile) {
                $resourceProfileFlat = new ResourceProfileFlat();
                $resourceProfileFlat->setProjectid($entity);
                $resourceProfileFlat->setWorkerid($resourceProfile->getWorkerid());
                $em->persist($resourceProfileFlat);

            }

            $em->flush();

            return $this->redirect($this->generateUrl('projects_details', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Edits an existing Projects entity.
     *
     * @Route("/{id}/update", name="projects_update")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:Projects:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Projects')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Projects entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new ProjectsType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('projects_details', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * @param $id
     *
     * @Route("/{id}/no-change", name="projects_visited")
     * @Method("GET")
     */
    public function projectsVisited($id)
    {
        $em = $this->getDoctrine()->getManager();
        if(!$entity = $em->getRepository('FullSixProjectForecastBundle:Projects')->find($id)) {
            throw new \Exception('Invalid project');
        }

        $entity->setProjectsupdated();
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('projects_details', array('id' => $id)));
    }

    /**
     * Deletes a Projects entity.
     *
     * // @Route("/{id}/delete", name="projects_delete")
     * // @Method("POST")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FullSixProjectForecastBundle:Projects')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Projects entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('projects'));
    }

    private function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

    /**
    * Edit in place
    * @Route("/edit-in-place", name="projects_edit_inplace")
    * @Method("POST")
    **/
    public function editInPlaceAction(Request $request)
    {
        $entity_name = 'FullSixProjectForecastBundle:'.$request->request->get('entity');
        $field_name = $request->request->get('attribut');
        $id = $request->request->get('id');
        $debug_output =  array();
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($entity_name)->find($id);

        if ($field_name == 'customersidcustomers') {
            $oldCustomer = $entity->getCustomersidcustomers();
        }

        $bundle_entity_name = explode(':', $entity_name);
        $entity_only_name = end($bundle_entity_name);

        $field_type = null;
        $field_options = array();
        if ($entity_only_name == 'Tasks') {
            if (in_array($field_name, array('tasksbeginat', 'tasksendat'))) {
                $field_type = 'date';
                $field_options = array(
                    'attr' => array(
                        'class' => 'has_datepicker set_readonly onselect_submit',
                    ),
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                );
            }
        }

        if ($field_name == 'tasksloadsworkers') {
            $resourceProfilesFlat = $em->getRepository('FullSixProjectForecastBundle:ResourceProfileFlat')->findBy(array(
                'projectid' => $entity->getTasksidtasks()->getProjectsidprojects()
                ));

            $workers = array();
            foreach ($resourceProfilesFlat as $resourceProfileFlat) {
                $workers[] = $resourceProfileFlat->getWorkerid();
            }

            $field_options =  array(
                'label' => "Affectation",
                'class' => 'FullSix\ProjectForecastBundle\Entity\Workers',
                'choices' => $workers,
                );
        }

        switch ($field_name) {
            case "customersidcustomers":
                $currId = $entity->getCustomersidcustomers() ? $entity->getCustomersidcustomers()->getId() : null;
                $workerId = null;
                if (!$this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
                    $workerId = $this->getUser()->getUser()->getWorker();
                }
                $field_options = $this->getActiveOptions('FullSixProjectForecastBundle:Customers', $currId, true, $workerId);
                break;
            case "providersidproviders":
                $currId = $entity->getProvidersidproviders() ? $entity->getProvidersidproviders()->getId() : null;
                $field_options = $this->getActiveOptions('FullSixProjectForecastBundle:Providers', $currId);
                break;
            case "projectstatus":
                $currId = $entity->getProjectstatus() ? $entity->getProjectstatus()->getId() : null;
                $field_options = $this->getActiveOptions('FullSixProjectForecastBundle:ProjectStatuses', $currId);
                break;
            case "jobsidjobs":
                $currId = $entity->getJobsidjobs() ? $entity->getJobsidjobs()->getId() : null;
                $field_options = $this->getActiveOptions('FullSixProjectForecastBundle:Jobs', $currId, true);
                break;
        }

        /*
        $form_type = "FullSix\ProjectForecastBundle\Form\\".$request->request->get('entity').'EditAjaxType';
        $editForm = $this->createForm(new $form_type, $entity);
        */
        $editForm = $this->createFormBuilder($entity)
        ->add($field_name, $field_type, $field_options)
        ->getForm();

        if( $request->request->get('form_name') )
        {
            $editForm->bind( $request->request->get( $editForm->getName() ));
            if ( $editForm->isValid() )
            {

                if ($field_name == 'customersidcustomers') {
                    $this->container->get('fullsix_projectforecast.resource_profile_helper')
                        ->changeProjectCustomer($oldCustomer, $entity->getCustomersidcustomers(), $entity);
                }

                $em->persist($entity);
                $em->flush();

                $render = $entity->get( $field_name );
                if( $render instanceof \DateTime )
                    $render = $render->format('d-m*-Y');
                $project_output = array();
                if ($entity_only_name == 'Tasks')
                {
                    $tasks = $em->getRepository('FullSixProjectForecastBundle:Tasks')->findByProjectsidprojects( $entity->getProjectsidprojects() );

                    $start_date = null;
                    $end_date = null;
                    foreach ($tasks as $task)
                    {
                        if(!$start_date)
                            $start_date = $task->getTasksbeginat();
                        if(!$end_date)
                            $end_date = $task->getTasksendat();

                        if($start_date > $task->getTasksbeginat())
                            $start_date = $task->getTasksbeginat();
                        if($end_date < $task->getTasksendat())
                            $end_date = $task->getTasksendat();
                    }


                    $project = $entity->getProject();
                    $project->setProjectfirsttasktdate($start_date);
                    $project->setProjectlasttasktdate($end_date);

                    $em->persist($project);
                    $em->flush();

                    $exit_interval = null;
                    $exit_interval2 = null;
                    if($project->getProjectfirsttasktdate() && $project->getProjectlasttasktdate())
                    {
                        $interval = date_diff($project->getProjectfirsttasktdate(), $project->getProjectlasttasktdate());
                        $interval2 = date_diff($project->getProjectfirsttasktdate(), new \DateTime());
                        $exit_interval = $interval->format('%a');
                        $exit_interval2 = $interval2->format('%a');
                    }

                    $project_output = array(
                        'projectfirsttasktdate' => $project->getProjectfirsttasktdate()->format('d F Y'),
                        'projectlasttasktdate' => $project->getProjectlasttasktdate()->format('d F Y'),
                        'interval_date' => $exit_interval,
                        'since_begining' => $exit_interval2,
                    );
                }


                $response = new Response(
                    json_encode(
                        array(
                            'status' => 'success',
                            'render' => $render,
                            'form_name' => $request->request->get('form_name'),
                            'project' => $project_output,
                            'entity' => $entity_only_name,
                            'item_id' => $id,
                            'field_name' => $field_name,
                            'debug' => $debug_output,
                            )
                        )
                    );
                $response->headers->set('Content-Type', 'application/json');
               return $response;
            }

            $response = new Response( json_encode( array('status' => 'error',
                'render' =>  $this->renderView('FullSixProjectForecastBundle:Projects:editinplace.html.twig', array(
                'entity' => $request->request->get('entity'),
                'id' => $id,
                'attribut' => $field_name,
                'form' => $editForm->createView(),
                )))));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }

        return $this->render('FullSixProjectForecastBundle:Projects:editinplace.html.twig', array(
            'entity' => $request->request->get('entity'),
            'id' => $id,
            'attribut' => $field_name,
            'form' => $editForm->createView(),
        ));

    }

    /*
     * @param $class : class name
     * @param $currId : the id of the current object
     * @param $required : boolean
     * @param $workerId : current logged in worker(used for filtering clients)
     * **/
    private function getActiveOptions($class, $currId, $required = false, $workerId = null){
        return  array(
            'class' => $class,
            'required' => $required,
            'query_builder' => function (EntityRepository $er) use ($currId, $workerId){
                $query = $er
                    ->createQueryBuilder('ent')
                    ->where('ent.isTrashed = 0');
                if ($currId) {
                    $query->orWhere('ent.id = :id')
                        ->setParameter(":id", $currId);
                }
                if($workerId){
                    $query->join('ent.resourceprofiles', 'rp');
                    $query->andWhere("rp.workerid = :workerid");
                    $query->setParameter('workerid', $workerId);
                }
                return $query;
            }
        );
    }

    /**
     * Trashed a Projects.
     *
     * @Route("/{id}/trashed", name="projects_trashed")
     * @Method("POST")

    public function trashedAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FullSixProjectForecastBundle:Projects')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Projects entity.');
            }

            $entity->setProjectsistrashed(true);
            $em->persist($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('projects_details', array('id' => $id)));
    }

    /**
     * Untrashed a Projects.
     *
     * @Route("/{id}/untrashed", name="projects_untrashed")
     * @Method("POST")

    public function untrashedAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('FullSixProjectForecastBundle:Projects')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Projects entity.');
            }

            $entity->setProjectsistrashed(false);
            $em->persist($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('projects_details', array('id' => $id)));
    }*/

//    public function isMyProject($project)
//    {
//        $createProject = 0;
//        $cotechsupervisor = 0;
//        if ($project instanceof Projects) {
//            if ($project->getProjectscotechoperational() instanceof Users) {
//                $cotechoperational = $project->getProjectscotechoperational()->getId();
//            }
//            if ($project->getProjectscotechsupervisor()  instanceof Users) {
//                $cotechsupervisor = $project->getProjectscotechsupervisor()->getId();
//            }
//            if ($cotechoperational == $this->getUser()->getId() || $cotechsupervisor == $this->getUser()->getId()) {
//                return true;
//            }
//        }
//
//        return false;
//    }

    /**
    * @Route("/{id}/activate/{page}/{customerId}", name="projects_activation")
    * @Template
    */
    public function activationAction($id, $page = null, $customerId = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:Projects')->find($id);

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
     * @Route("/merge-popup/{id}", name="projects_merge_popup")
     */
    public function popupMergeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:Projects')->find($id);

        $projects = $em->getRepository('FullSixProjectForecastBundle:Projects')->getActiveProjectsForMerge($id);

        return $this->render('FullSixProjectForecastBundle:Projects:mergePopup.html.twig', array(
            'entity' => $entity,
            'projects' => $projects,
        ));
    }

    /**
     * Merging Action
     *
     * @Route("/merge/{id}", name="projects_merge")
     * @Method("POST");
     */
    public function mergeAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->get('merge') == "destination") {
            $mergedProject = $em->getRepository('FullSixProjectForecastBundle:Projects')->find($id);
            $masterProject = $em->getRepository('FullSixProjectForecastBundle:Projects')->find($request->get('project'));
        } elseif ($request->get('merge') == "source") {
            $masterProject = $em->getRepository('FullSixProjectForecastBundle:Projects')->find($id);
            $mergedProject = $em->getRepository('FullSixProjectForecastBundle:Projects')->find($request->get('project'));
        } else {
             throw new Exception('Please select the merge source and destination');
        }

        $mergedProject->setMergedWith($masterProject);
        $mergedProject->setIsTrashed(true);

        foreach($mergedProject->getKeydates() as $mergedKeyDate) {
            $mergedKeyDate->setProjects($masterProject);
        }

        foreach($mergedProject->getProjectstasks() as $mergedTask) {
            $mergedTask->setProjectsidprojects($masterProject);
        }

        $em->persist($mergedProject);
        $em->flush();

        return $this->redirect($this->generateUrl('projects'));
    }
}

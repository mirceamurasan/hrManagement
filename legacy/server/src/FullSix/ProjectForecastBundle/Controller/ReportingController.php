<?php

namespace FullSix\ProjectForecastBundle\Controller;

use APY\DataGridBundle\Grid\Action\RowAction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Source\Vector;
use APY\DataGridBundle\Grid\Column;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FullSix\ProjectForecastBundle\Form\PrevNextDateType;
use FullSix\ProjectForecastBundle\Form\JobsFilterType;

/**
 * Reporting controller
 *
 * @Route("/reporting")
 */
class ReportingController extends Controller
{

    /**
     * @Route("/", name="reporting")
     * @Template()
     */
    public function indexAction()
    {
        return $this->redirect($this->generateUrl('home'));
    }

    /**
     * @Route("/workload/{jobid}/{intext}/{date}/{nbweek}", defaults={"intext"="all", "date"="NOW", "nbweek"=4}, name="workload")
     * @Template()
     */
    public function workloadAction($jobid, $intext, $date, $nbweek)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->container->get('request_stack')->getCurrentRequest();

        // Initialisation de l'objet du plan de charge
        if (!$job = $em->getRepository('FullSixProjectForecastBundle:Jobs')->findOneById($jobid))
        {
            throw new \Exception(sprintf("L'objet %s n'est pas défini pour l'ID %d", 'Jobs', $jobid));
        }

        // Initialisation de la date au premier jour de la semaine courrante ou demandée à minuit
        // par défaut la valeur de 'date' est à NOW
        $date = new \DateTime($date, new \DateTimeZone('Europe/Paris'));
        while ($date->format('w') != 1) {
            $date->sub(new \DateInterval('P1D'));
        }
        $date->setTime(0, 0, 0);

        $form = $this->createForm(new PrevNextDateType());

        //set default view period the current week
        $monday = new \DateTime("monday this week");
        $sunday = new \DateTime("sunday this week");

        $form->get('startDate')->setData($monday->format('Y-m-d'));
        //logic is in PRE_SUBMIT listener because otherwise we are unable to modify a
        // submitted form to update forms startDate
        $form->handleRequest($request);

        if ($form->isValid()) {
            $monday = new \DateTime($form->get('startDate')->getData());
            $sunday = new \DateTime($form->get('startDate')->getData());
            $sunday->modify('+6 days');
        }

        $param = array();
        $param['form'] = $form->createView();
        $param['weekStartDate'] = $monday->format("Y-m-d");
        $param['weekEndDate'] = $sunday->format("Y-m-d");


        $param['jobid'] = $job->getId();
        $param['intext'] = $intext;

        $startDate = $monday;
        $endDate = $sunday;

        //$entity = $em->getRepository('FullSixProjectForecastBundle:Jobs')->planDeChargeByJobsIdOnWeeks($jobid, $date, $nbweek, $intext);

        $chargeData = array('CustomersName' => "testCustomer", 'ProjectsId' => 0, 'ProjectsName' => "testProject", 'CotechOperationalUserName' => "coUserName", 'SuperviseurUserName' => 'sUserName', 'ProjectsSuccess' => 97);

        for ($i = 1; $i <= $nbweek; $i++) {
            if ($i > 1)
            {
                $startDate->modify('+7 days');
                $endDate->modify('+7 days');
            }

            $param['internalDispo'][$i] = $this->container->get('fullsix_projectforecast.availability_computation')->getTeamMemberWeeklyAvailabilityByJobId(
                                                                                                                     $jobid,
                                                                                                                     clone $startDate,
                                                                                                                     clone $endDate
            );

            $param['externalDispo'][$i] = $this->container->get('fullsix_projectforecast.availability_computation')->getServiceProviderWeeklyAvailabilityByJobId(
                                                                                                                     $jobid,
                                                                                                                     clone $startDate,
                                                                                                                     clone $endDate
            );

            $chargeData['week_' . $i] = $param['internalDispo'][$i]['DaysDispo'] + $param['externalDispo'][$i]['DaysDispo'];
        }

        $entity = array($chargeData);


        for ($i = 1; $i <= $nbweek; $i++) {

            $total = $param['total'][] = array_reduce($entity, create_function('$total, $next', '$total += $next["week_' . $i . '"]; return round($total, 2);'));
            $dispo = 0;
            $dispo += ($param['internalDispo'][$i]['DaysDispo'] > 0 ? $param['internalDispo'][$i]['DaysDispo'] : 0);
            $dispo += ($param['externalDispo'][$i]['DaysDispo'] > 0 ? $param['externalDispo'][$i]['DaysDispo'] : 0);
            $param['percent'][] = $dispo > 0 ? round($total / $dispo * 100) : 0;
        }

        $columns[] = new Column\TextColumn(array('id' => 'CustomersName', 'field' => 'CustomersName', 'source' => true, 'title' => 'Customer'));
        $columns[] = new Column\NumberColumn(array('id' => 'ProjectsId', 'field' => 'ProjectsId', 'source' => true, 'visible' => false));
        $columns[] = new Column\TextColumn(array('id' => 'ProjectsName', 'field' => 'ProjectsName', 'source' => true, 'title' => 'Project'));
        $columns[] = new Column\TextColumn(array('id' => 'CotechOperationalUserName', 'field' => 'CotechOperationalUserName', 'source' => true, 'title' => 'CoTech Opérationnel'));
        $columns[] = new Column\TextColumn(array('id' => 'SuperviseurUserName', 'field' => 'SuperviseurUserName', 'source' => true, 'title' => 'Superviseur'));
        $columns[] = new Column\NumberColumn(array('id' => 'ProjectsSuccess', 'field' => 'ProjectsSuccess', 'source' => true, 'title' => "Succes"));
        for ($i = 1; $i <= $nbweek; $i++) {
            $columns[] = new Column\NumberColumn(array('id' => 'week_' . $i, 'field' => 'week_' . $i, 'source' => true, 'title' => $date->format('W - d/m/Y'), 'precision' => 2));
            $date->add(new \DateInterval('P1W'));
        }

        $source = new Vector($entity, $columns);
        $grid = $this->get('grid');
        $grid->setSource($source);
        $grid->hideFilters();
        $grid->setDefaultOrder('CustomersName', 'ASC');

        return $grid->getGridResponse('FullSixProjectForecastBundle:Reporting:workload.html.twig', array('title' => 'Last modification "' . $job->getJobslabel() . '"', 'param' => $param));
    }

    /**
     * @Route("/daysoffs/{date}/{nbWeeks}", defaults={"date"=null, "nbWeeks"=2}, name="reporting_daysoffs")
     * @Template()
     */
    public function daysoffsAction($date, $nbWeeks)
    {
        if (is_null($date)) {
            $date_deb = new \DateTime(date('Y-m-d'));
        } else {
            $date_deb = new \DateTime($date);
        }

        $tab_days = $tab_nbUsr = array();
        $oldJobId = 0;

        $em = $this->getDoctrine()->getManager();

        // FILTER USERS BY JOB
        $allJobs = $em->getRepository('FullSixProjectForecastBundle:Jobs')->findAll();
        $jobsList = array('None');
        foreach ($allJobs as $job) {
            $jobsList[$job->getId()] = $job->getJobslabel();
        }
        $jobsFilterForm = $this->createForm(new JobsFilterType($jobsList));

        //set default view period
        $jobsFilterForm->get('startDate')->setData($date_deb->format('Y-m-d'));

        $jobsFilterForm->handleRequest($this->getRequest());
        $selectedJobs = array();
        if ($jobsFilterForm->isValid()) {
            //get selected jobs and update startDate of table accordingly to submitted data
            $selectedJobs = $jobsFilterForm->get('jobsSelect')->getNormData();
            $date_deb = new \DateTime($jobsFilterForm->get('startDate')->getData());
        }

        // set endDate accordingly to submitted form
        $date_fin = clone $date_deb;
        $date_fin->add(new \DateInterval('P' . $nbWeeks . 'W'));
        $date_tst = clone $date_deb;

        // GET GLOBAL DAYS OFF /////////////////////////////////////////////////
        $daysoffsglobal = $em->getRepository('FullSixProjectForecastBundle:DaysoffsGlobal')->findAllDaysoffsBetweenTwoDates($date_deb, $date_fin);
        $tab_daysoffsglobal = array();
        foreach ($daysoffsglobal as $dayoff) {
            $tab_daysoffsglobal[$dayoff->getDaysoffsGlobalDate()->format('Y-m-d')] = true;
        }

        // Get Periods entities in viewing time period
        $contractPeriods = $em->getRepository('FullSixProjectForecastBundle:Periods')->findPeriodsMatchingJobsInTimePeriod($selectedJobs, $date_deb, $date_fin);

        $contractPeriodsArray = $tab_workerVacations = array();
        $interval = \DateInterval::createFromDateString('1 day');
        foreach ($contractPeriods as $contractPeriod) {
            // Get workers with the selected jobs
            $worker = $contractPeriod->getIdcontract()->getIdworker();
            $workerId = $worker->getId();

            // GET WORKERS DAYS OFF only for team members////////////////////////////////////////////
            if(!$contractPeriod->getServiceprovider()){
                foreach ($contractPeriod->getPeriodvacations() as $vacation) {
                    //get vacation period
                    $begin = $vacation->getStartdate();
                    $end = clone($vacation->getEnddate());
                    $end->modify('+1 day'); // we add one day for calculated interval to be exact
                    $period = new \DatePeriod($begin, $interval, $end);
                    //we have to mark each day of vacation in tab_workerVacations array
                    foreach ($period as $dayOff) {
                        if ($vacation->getMorning()) {
                            $tab_workerVacations[$workerId][$dayOff->format('Y-m-d')]['M'] = true;
                        }
                        if ($vacation->getAfternoon()) {
                            $tab_workerVacations[$workerId][$dayOff->format('Y-m-d')]['A'] = true;
                        }
                    }
                }
            }

            if(array_key_exists($workerId,$contractPeriodsArray) ){
                $workerAlreadyPeriods = $contractPeriodsArray[$workerId]['periods'];
                array_push($workerAlreadyPeriods,
                        array(
                            'startDate' => $contractPeriod->getStartdate(),
                            'endDate' => $contractPeriod->getEnddate()
                        ));
                $contractPeriodsArray[$workerId]['periods'] = $workerAlreadyPeriods;
            }else{
                $contractPeriodsArray[$workerId] = array(
                    'workerDisplayName' => $worker->getUser()->getCompleteName(),
                    'jobId' => $contractPeriod->getIdcontract()->getIdjob()->getId(),
                    'jobLabel' => $contractPeriod->getIdcontract()->getIdjob()->getJobslabel(),
                    'periods'=> array(
                        array(
                            'startDate' => $contractPeriod->getStartdate(),
                            'endDate' => $contractPeriod->getEnddate()
                        )
                    )
                );
            }
        } // end foreach contract period

        // CALCULATE TOTALS
        while ($date_tst <= $date_fin) {
            $tab_days[$date_tst->format('Y-m-d')] = clone $date_tst;

            //sous total par job pour chaque jour
            $tab_nbUsr[0][$date_tst->format('Y-m-d')]['M'] = 0; // MORNING
            $tab_nbUsr[0][$date_tst->format('Y-m-d')]['A'] = 0; // AFTERNOON
            $tab_nbUsr[0][$date_tst->format('Y-m-d')]['G'] = 0; // TOTAL WORKERS

            foreach ($contractPeriodsArray as $workerId => $contractPeriod) {

                // initialize count or reset number of workers per job if we move to new job
                if ($oldJobId != $contractPeriod['jobId'] || !isset($tab_nbUsr[$contractPeriod['jobId']][$date_tst->format('Y-m-d')])) {
                    $tab_nbUsr[$contractPeriod['jobId']][$date_tst->format('Y-m-d')]['M'] = 0;
                    $tab_nbUsr[$contractPeriod['jobId']][$date_tst->format('Y-m-d')]['A'] = 0;
                    $tab_nbUsr[$contractPeriod['jobId']][$date_tst->format('Y-m-d')]['G'] = 0;
                }
                // If user is active in this day
                foreach($contractPeriod['periods'] as $onePeriod){
                    if ($date_tst >= $onePeriod['startDate'] && ($date_tst <= $onePeriod['endDate'] || $onePeriod['endDate'] == null)) {
                        // if not global dayoff and not weekend worker should be available
                        if (!isset($tab_daysoffsglobal[$date_tst->format('Y-m-d')]) && !($date_tst->format('N') == '6' || $date_tst->format('N') == '7')) {
                            // if worker not in vacation, he is avaiable
                            if (!isset($tab_workerVacations[$workerId][$date_tst->format('Y-m-d')]['M'])) {
                                // worker available in the morning
                                $tab_nbUsr[$contractPeriod['jobId']][$date_tst->format('Y-m-d')]['M'] += 1;
                            }
                            if (!isset($tab_workerVacations[$workerId][$date_tst->format('Y-m-d')]['A'])) { // pas de cp l'après midi
                                // worker available in the evening
                                $tab_nbUsr[$contractPeriod['jobId']][$date_tst->format('Y-m-d')]['A'] += 1;
                            }
                        }
                        $tab_nbUsr[$contractPeriod['jobId']][$date_tst->format('Y-m-d')]['G'] += 1;
                    }
                }

                $oldJobId = $contractPeriod['jobId'];
            }
            $date_tst->add(new \DateInterval('P1D'));
        }

        return array(
            'datas' => array(
                'contractPeriods' => $contractPeriodsArray,
                'tabDaysOffUsers' => $tab_workerVacations,
                'tabDaysOffGlobal' => $tab_daysoffsglobal,
                'tabDays' => $tab_days,
                'tabNbUsr' => $tab_nbUsr,
                'filterForm' => $jobsFilterForm->createView(),
                'viewingPeriodStartDate' => $date_deb->format('Y-m-d'),
                'viewingPeriodEndDate' => $date_fin->format('Y-m-d'),
            ),
        );
    }

    /**
     * @Route("/revuehebdo", name="reporting_revuehebdo")
     * @Template()
     */
    public function revuehebdoAction(Request $request)
    {
        $workerId = null;
        $em = $this->getDoctrine()->getManager();
        $workers = $em->getRepository('FullSixProjectForecastBundle:Workers')->getWorkersWithActiveTaskLoads();
        $workersArray = array(
            null => 'reporting.weekly.options.any',
            0 => 'reporting.weekly.options.unassigned'
        );
        foreach ($workers as $worker) {
            $workersArray[$worker->getId()] = $worker->getUser()->getCompleteName();
        }

        $form = $this->createForm(new PrevNextDateType($workersArray));

        //set default view period the current week
        $monday = new \DateTime("monday this week");
        $form->get('startDate')->setData($monday->format('Y-m-d'));

        //logic is in PRE_SUBMIT listener because otherwise we are unable to modify a
        // submitted form to update forms startDate
        $form->handleRequest($request);

        if ($form->isValid()) {
            $monday = new \DateTime($form->get('startDate')->getData());
            $workerId = $form->get('workers')->getData();
        }
        $projects = $em->getRepository('FullSixProjectForecastBundle:Projects')->findNoTrashProjectsByWorkerAndTimePeriod($workerId, $monday);
        $projectsIds = array(0);
        foreach ($projects as $project) {
            $projectsIds[] = $project->getId();
        }

        $relatedTasks = $this->get('fullsix_projectforecast.project_helper')->getProjectsRelatedTasksByWorkerArray($projectsIds, $workerId);

        $vacations_entities = $em->getRepository('FullSixProjectForecastBundle:Vacations')->getVacationsByWorkersInTimePeriod(array($workerId => $workerId), $monday);
        $vacations = array();
        foreach ($vacations_entities as $vacation) {
            $vacations[] = array(
                'startDate' => $vacation->getStartdate(),
                'endDate' => $vacation->getEndDate(),
                'status' => $vacation->getTypeOfVacationAsString(),
            );
        }

        return array(
            'title' => 'Revue hebdo',
            'workers' => $workers,
            'currentlyViewed' => $workerId,
            'projects' => $projects,
            'tasks' => $relatedTasks['tasks'],
            'tasksloads' => $relatedTasks['tasksLoads'],
            'out_tasksloads' => $relatedTasks['outTasksLoads'],
            'form' => $form->createView(),
            'weekStartDate' => $monday->format("Y-m-d"),
            'weekEndDate' => $monday->modify('+6 days')->format("Y-m-d"),
            'vacations' => $vacations,
        );
    }


    /**
     * Lists all ProjectImportReport entities.
     *
     * @Route("/projectimportreports/", name="project_import_reports")
     * @Template()
     */
    public function projectreportsAction () {

        $source = new Entity('FullSixProjectForecastBundle:ProjectImportReports');
        $grid = $this->get('grid');
        $grid->setSource($source);
        $grid->setLimits(10);

        $grid->setActionsColumnSeparator("&nbsp;&nbsp;");

        $grid->setDefaultOrder("importDate", "asc");

        $grid->getColumn('csvPath')->manipulateRenderCell(function($value, $row, $router) {
            return basename($value);
        });

        $grid->getColumn('pdfPath')->manipulateRenderCell(function($value, $row, $router) {
            return basename($value);
        });

        $rowActionCSV = new RowAction("download-alt", 'project_import_csv', false, "_self",
                                   array("title" => "Download CSV"));
        $rowActionCSV->setRouteParameters(array('id'));
        $grid->addRowAction($rowActionCSV);

        $rowActionLog = new RowAction("download", 'project_import_log', false, "_self",
                                   array("title" => "Download Log File"));

        $grid->addRowAction($rowActionLog);

        return $grid->getGridResponse('FullSixProjectForecastBundle::grid.html.twig',
                                      array(
                                          'title' => 'Project Import Logs',
                                      )
        );
    }

    /**
     * Downloads CSV file.
     *
     * @Route("/projectimportreports/download/csv/{id}", name="project_import_csv")
     * @Template()
     */
    public function downloadcsvAction($id) {
        $fileDownloadService = $this->container->get('fullsix_projectforecast.file_download');
        $response = $fileDownloadService->downloadProjectImportCsv($id);

        if (is_array($response) && !empty($response['message'])) {
            $this->get('session')->getFlashBag()->add('errors', $response['message']);
            return $this->redirect($this->generateUrl('project_import_reports'));
        } else {
            return $response;
        }
    }

    /**
     * Downloads log file
     *
     * @Route("/projectimportreports/download/log/{id}", name="project_import_log")
     * @Template()
     */
    public function downloadlogAction($id) {

        $fileDownloadService = $this->container->get('fullsix_projectforecast.file_download');
        $response = $fileDownloadService->downloadProjectImportPdf($id);

        if (is_array($response) && !empty($response['message'])) {
            $this->get('session')->getFlashBag()->add('errors', $response['message']);
            return $this->redirect($this->generateUrl('project_import_reports'));
        } else {
            return $response;
        }
    }

}
<?php

namespace FullSix\ProjectForecastBundle\Helper;

class ProjectHelper
{

    protected $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function getProjectsRelatedTasksByWorkerArray($projectsIds, $workerId)
    {
        $tasks_entities = $this->em->getRepository('FullSixProjectForecastBundle:Tasks')->findByProjectIdsAndWorkerOrderByDateAndName($projectsIds, $workerId);
        $tasksIds = array(0);
        $tasks = array();
        foreach ($tasks_entities as $task) {
            $tasksIds[] = $task->getId();
            $tasks[$task->getProjectsidprojects()->getId()][$task->getId()] = array(
                'id' => $task->getId(),
                'projectid' => $task->getProjectsidprojects()->getId(),
                'name' => $task->getTasksname(),
                'datedeb' => $task->getTasksbeginat(),
                'datefin' => $task->getTasksendat(),
            );
        }

        $tasksloads_entities = $this->em->getRepository('FullSixProjectForecastBundle:Tasksloads')->findTotalVolumeTaskIdGroupByJobs($tasksIds);
        $tasksloads = array();
        foreach ($tasksloads_entities as $tasksload) {
            $tasksloads[$tasksload['taskid']][$tasksload['jobid']] = array(
                'jobslabel' => $tasksload['jobslabel'],
                'volume' => $tasksload['volume'],
            );
        }

        $out_tasksloads_entities = $this->em->getRepository('FullSixProjectForecastBundle:Tasksloads')->findOutVolumeTaskIdGroupByJobs($tasksIds);
        $out_tasksloads = array();
        foreach ($out_tasksloads_entities as $out_tasksload) {
            $out_tasksloads[$out_tasksload['taskid']][$out_tasksload['jobid']] = array(
                'jobslabel' => $out_tasksload['jobslabel'],
                'volume' => $out_tasksload['volume'],
            );
        }

        return array(
            'tasks' => $tasks,
            'tasksLoads' => $tasksloads,
            'outTasksLoads' => $out_tasksloads,
        );
    }

}

<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use APY\DataGridBundle\Grid\Mapping as GRID;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * FullSix\ProjectForecastBundle\Entity\Tasksloads
 *
 * @ORM\Table(name="TasksLoads")
 * @ORM\Entity(repositoryClass="FullSix\ProjectForecastBundle\Repository\TasksloadsRepository")
 * @ORM\HasLifecycleCallbacks
 *
 * @GEDMO\Loggable(logEntryClass="FullSix\ProjectForecastBundle\Entity\PfLogEntries")
 *
 * @GRID\Source(columns="tasksidtasks.tasksname, jobsidjobs.jobslabel, tasksloadsvolume,tasksloadsworkers.user, providersidproviders.providersname, isTrashed, id")
 */
class Tasksloads extends NonDeletableEntity{

    /**
     * @var integer $id
     *
     * @ORM\Column(name="Id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $id;

    /**
     * @var integer $tasksloadsvolume
     *
     * @GEDMO\Versioned
     *
     * @ORM\Column(name="TasksLoadsVolume", type="smallint", nullable=false)
     * @Assert\NotBlank()
     * @GRID\Column(title="Volume")
     */
    private $tasksloadsvolume;

    /**
     * @var \DateTime $tasksloadscreated
     *
     * @ORM\Column(name="TasksLoadsCreated", type="datetime", nullable=false)
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $tasksloadscreated;

    /**
     * @var \DateTime $tasksloadsupdated
     *
     * @ORM\Column(name="TasksLoadsUpdated", type="datetime", nullable=true)
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $tasksloadsupdated;

    /**
     * @var Tasks
     *
     * @ORM\ManyToOne(targetEntity="Tasks", inversedBy="id", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="TasksIdTasks", referencedColumnName="Id", nullable=false)
     * })
     *
     * @GEDMO\Versioned
     *
     * @GRID\Column(field="tasksidtasks.tasksname", title="Tache")
     */
    private $tasksidtasks;

    /**
     * @var Workers
     *
     * @ORM\ManyToOne(targetEntity="Workers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="TasksLoadsWorkers", referencedColumnName="id")
     * })
     *
     * @GEDMO\Versioned
     *
     * @GRID\Column(field="tasksloadsworkers.user.username", title="Attribué à")
     */
    private $tasksloadsworkers;

    /**
     * @var Providers
     *
     * @ORM\ManyToOne(targetEntity="Providers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ProvidersIdProviders", referencedColumnName="Id")
     * })
     *
     * @GEDMO\Versioned
     *
     * @GRID\Column(field="providersidproviders.providersname", title="Prestataire")
     */
    private $providersidproviders;

    /**
     * @var Login
     *
     * @ORM\ManyToOne(targetEntity="Login")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="TasksLoadsCreatedBy", referencedColumnName="id")
     * })
     *
     * @GRID\Column(field="agenciescreatedby.username", title="Créer par", filterable=false)
     */
    private $tasksloadscreatedby;

    /**
     * @var Jobs
     *
     * @ORM\ManyToOne(targetEntity="Jobs", inversedBy="taskloads", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="JobsIdJobs", referencedColumnName="Id", nullable=false)
     * })
     *
     * @GEDMO\Versioned
     *
     * @GRID\Column(field="jobsidjobs.jobslabel", title="Métier")
     */
    private $jobsidjobs;

    public function __construct() {
    }

    /**
     * Get tasksname
     *
     *
     */
    public function get( $input ) {

        switch ( $input ) {
            case 'jobsidjobs':
                return $this->getJobsidjobs()->__toString();
                break;
            case 'tasksloadsvolume':
                return $this->getTasksloadsvolume();
                break;
            case 'tasksloadsworkers':
                return $this->getTasksloadsworkers()->__toString();
                break;
            case 'providersidproviders':
                return $this->getProvidersidproviders()->__toString();
                break;
            default:
                return '';
                break;
        }
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set tasksloadsvolume
     *
     * @param integer $tasksloadsvolume
     * @return Tasksloads
     */
    public function setTasksloadsvolume($tasksloadsvolume) {
        $this->tasksloadsvolume = $tasksloadsvolume;

        return $this;
    }

    /**
     * Get tasksloadsvolume
     *
     * @return integer
     */
    public function getTasksloadsvolume() {
        return $this->tasksloadsvolume;
    }

    /**
     * Set tasksloadscreated
     *
     * @ORM\PrePersist
     * @return Tasksloads
     */
    public function setTasksloadscreated() {
        $this->tasksloadscreated = new \DateTime();

        return $this;
    }

    /**
     * Get tasksloadscreated
     *
     * @return \DateTime
     */
    public function getTasksloadscreated() {
        return $this->tasksloadscreated;
    }

    /**
     * Set tasksloadsupdated
     *
     * @ORM\PreUpdate
     * @return Tasksloads
     */
    public function setTasksloadsupdated() {
        $this->tasksloadsupdated = new \DateTime();

        return $this;
    }

    /**
     * Get tasksloadsupdated
     *
     * @return \DateTime
     */
    public function getTasksloadsupdated() {
        return $this->tasksloadsupdated;
    }

    /**
     * Set tasksidtasks
     *
     * @param FullSix\ProjectForecastBundle\Entity\Tasks $tasksidtasks
     * @return Tasksloads
     */
    public function setTasksidtasks(\FullSix\ProjectForecastBundle\Entity\Tasks $tasksidtasks = null) {
        $this->tasksidtasks = $tasksidtasks;

        return $this;
    }

    /**
     * Get tasksidtasks
     *
     * @return FullSix\ProjectForecastBundle\Entity\Tasks
     */
    public function getTasksidtasks() {
        return $this->tasksidtasks;
    }

    /**
     * get Worker
     *
     * @return \FullSix\ProjectForecastBundle\Entity\Workers
     */
    public function getTasksloadsworkers() {
        return $this->tasksloadsworkers;
    }

    /**
     * Set Worker
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Workers $tasksloadsworkers
     * @return \FullSix\ProjectForecastBundle\Entity\Tasksloads
     */
    public function setTasksloadsworkers(\FullSix\ProjectForecastBundle\Entity\Workers $tasksloadsworkers = null) {
        $this->tasksloadsworkers = $tasksloadsworkers;

        return $this;
    }

    /**
     * Set providersidproviders
     *
     * @param FullSix\ProjectForecastBundle\Entity\Providers $providersidproviders
     * @return Tasksloads
     */
    public function setProvidersidproviders(\FullSix\ProjectForecastBundle\Entity\Providers $providersidproviders = null)
    {
        $this->providersidproviders = $providersidproviders;
//        $this->tasksidtasks->getProjectsidprojects()->isOutsourcing();

        return $this;
    }

    /**
     * Get providersidproviders
     *
     * @return FullSix\ProjectForecastBundle\Entity\Providers
     */
    public function getProvidersidproviders() {
        return $this->providersidproviders;
    }

    /**
     * Set tasksloadscreatedby
     *
     * @param FullSix\ProjectForecastBundle\Entity\Login $tasksloadscreatedby
     * @return Tasksloads
     */
    public function setTasksloadscreatedby(\FullSix\ProjectForecastBundle\Entity\Login $tasksloadscreatedby = null) {
        $this->tasksloadscreatedby = $tasksloadscreatedby;

        return $this;
    }

    /**
     * Get tasksloadscreatedby
     *
     * @return FullSix\ProjectForecastBundle\Entity\Login
     */
    public function getTasksloadscreatedby() {
        return $this->tasksloadscreatedby;
    }

    /**
     * Set jobsidjobs
     *
     * @param FullSix\ProjectForecastBundle\Entity\Jobs $jobsidjobs
     * @return Tasksloads
     */
    public function setJobsidjobs(\FullSix\ProjectForecastBundle\Entity\Jobs $jobsidjobs = null) {
        $this->jobsidjobs = $jobsidjobs;

        return $this;
    }

    /**
     * Get jobsidjobs
     *
     * @return FullSix\ProjectForecastBundle\Entity\Jobs
     */
    public function getJobsidjobs() {
        return $this->jobsidjobs;
    }

    /**
     * check if the entity can be (de)activated
     */
    public function checkActivation(){
        $err = array();

        if($this->getIsTrashed() == true){
            if ($this->getTasksidtasks()->getIsTrashed() == true) {
                array_push($err, "project.taskload.cannotActivate.inactiveTask");
            }
            if ($this->getJobsidjobs()->getIsTrashed() == true) {
                array_push($err, "project.taskload.cannotActivate.inactiveJob");
            }
        }else{
            //test for deactivation
        }

        return $err;
    }
}
<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;
use FullSix\ProjectForecastBundle\Entity\NonDeletableEntity;

/**
 * FullSix\ProjectForecastBundle\Entity\Tasks
 *
 * @ORM\Table(name="Tasks")
 * @ORM\Entity(repositoryClass="FullSix\ProjectForecastBundle\Repository\TasksRepository")
 * @ORM\HasLifecycleCallbacks
 *
 * @GEDMO\Loggable(logEntryClass="FullSix\ProjectForecastBundle\Entity\PfLogEntries")
 *
 * @Assert\Callback(methods={"isIntervalValid"})
 *
 * @GRID\Source(columns="tasksname, projectsidprojects.projectsname, tasksbeginat, tasksendat, taskscreatedby.username, isTrashed, id")
 */
class Tasks extends NonDeletableEntity
{

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
     * @var string $tasksname
     *
     * @ORM\Column(name="TasksName", type="string", length=45, nullable=false)
     *
     * @GEDMO\Versioned
     *
     * @Assert\NotBlank()
     * @GRID\Column(title="Nom")
     */
    private $tasksname;

    /**
     * @var \DateTime $tasksbeginat
     *
     * @ORM\Column(name="TasksBeginAt", type="date", nullable=false)
     *
     * @GEDMO\Versioned
     *
     * @Assert\NotBlank()
     * @GRID\Column(title="Débute le")
     */
    private $tasksbeginat;

    /**
     * @var \DateTime $tasksendat
     *
     * @ORM\Column(name="TasksEndAt", type="date", nullable=false)
     *
     * @GEDMO\Versioned
     *
     * @Assert\NotBlank()
     * @GRID\Column(title="Finis le")
     */
    private $tasksendat;

    /**
     * @var integer $tasksorder
     *
     * @ORM\Column(name="TasksOrder", type="smallint", nullable=false)
     *
     * @GRID\Column(title="Ordre")
     */
    private $tasksorder;

    /**
     * @var \DateTime $taskscreated
     *
     * @ORM\Column(name="TasksCreated", type="datetime", nullable=false)
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $taskscreated;

    /**
     * @var \DateTime $tasksupdated
     *
     * @ORM\Column(name="TasksUpdated", type="datetime", nullable=true)
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $tasksupdated;


    /**
     * @var Projects
     *
     * @ORM\ManyToOne(targetEntity="Projects", inversedBy="id", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ProjectsIdProjects", referencedColumnName="Id")
     * })
     *
     * @GEDMO\Versioned
     *
     * @GRID\Column(field="projectsidprojects.projectsname", title="Project")
     */
    private $projectsidprojects;

    /**
     * @var Users
     *
     * @ORM\ManyToOne(targetEntity="Login")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="TasksCreatedBy", referencedColumnName="id")
     * })
     *
     * @GRID\Column(field="taskscreatedby.username", title="Créer par", filterable=false)
     */
    private $taskscreatedby;

    /**
     * @ORM\OneToMany(targetEntity="Tasksloads", mappedBy="tasksidtasks")
     * @ORM\OrderBy({"tasksloadsvolume" = "DESC"})
     */
    protected $taskstasksloads;

    public function __construct() {
        $this->taskstasksloads = new ArrayCollection();
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
     * Set tasksname
     *
     * @param string $tasksname
     * @return Tasks
     */
    public function setTasksname($tasksname) {
        $this->tasksname = $tasksname;

        return $this;
    }

    /**
     * Get tasksname
     *
     * @return string
     */
    public function getTasksname() {
        return $this->tasksname;
    }

    /**
     * Getter generique restraint aux champs possibles
     *
     *
     */
    public function get( $input ) {

         switch ( $input ) {
            case 'tasksname':
                return $this->getTasksname();
                break;
            case 'tasksbeginat':
                return $this->getTasksbeginat()->format('d-m-Y');
                break;
            case 'tasksendat':
                return $this->getTasksendat()->format('d-m-Y');
                break;
            default:
                return '';
                break;
        }

    }


    /**
     * Set tasksbeginat
     *
     * @param \DateTime $tasksbeginat
     * @return Tasks
     */
    public function setTasksbeginat($tasksbeginat) {
        $this->tasksbeginat = $tasksbeginat;

        return $this;
    }

    /**
     * Get tasksbeginat
     *
     * @return \DateTime
     */
    public function getTasksbeginat() {
        return $this->tasksbeginat;
    }

    /**
     * Set tasksendat
     *
     * @param \DateTime $tasksendat
     * @return Tasks
     */
    public function setTasksendat($tasksendat) {
        $this->tasksendat = $tasksendat;

        return $this;
    }

    /**
     * Get tasksendat
     *
     * @return \DateTime
     */
    public function getTasksendat() {
        return $this->tasksendat;
    }

    /**
     * Set tasksorder
     *
     * @param integer $tasksorder
     * @return Tasks
     */
    public function setTasksorder($tasksorder) {
        $this->tasksorder = $tasksorder;

        return $this;
    }

    /**
     * Get tasksorder
     *
     * @return integer
     */
    public function getTasksorder() {
        return $this->tasksorder;
    }

    /**
     * Set taskscreated
     *
     * @ORM\PrePersist
     * @return Tasks
     */
    public function setTaskscreated() {
        $this->taskscreated = new \DateTime();

        return $this;
    }

    /**
     * Get taskscreated
     *
     * @return \DateTime
     */
    public function getTaskscreated() {
        return $this->taskscreated;
    }

    /**
     * Set tasksupdated
     *
     * @ORM\PreUpdate
     * @return Tasks
     */
    public function setTasksupdated() {
        $this->tasksupdated = new \DateTime();

        return $this;
    }

    /**
     * Get tasksupdated
     *
     * @return \DateTime
     */
    public function getTasksupdated() {
        return $this->tasksupdated;
    }


    /**
     * Set projectsidprojects
     *
     * @param FullSix\ProjectForecastBundle\Entity\Projects $projectsidprojects
     * @return Tasks
     */
    public function setProjectsidprojects(\FullSix\ProjectForecastBundle\Entity\Projects $projectsidprojects = null) {
        $this->projectsidprojects = $projectsidprojects;

        return $this;
    }

    /**
     * Get projectsidprojects
     *
     * @return FullSix\ProjectForecastBundle\Entity\Projects
     */
    public function getProjectsidprojects() {
        return $this->projectsidprojects;
    }

    /**
     * Get project
     *
     * @return FullSix\ProjectForecastBundle\Entity\Projects
     */
    public function getProject() {
        return $this->projectsidprojects;
    }

    /**
     * Set taskscreatedby
     *
     * @param FullSix\ProjectForecastBundle\Entity\Users $taskscreatedby
     * @return Tasks
     */
    public function setTaskscreatedby(\FullSix\ProjectForecastBundle\Entity\Login $taskscreatedby = null) {
        $this->taskscreatedby = $taskscreatedby;

        return $this;
    }

    /**
     * Get taskscreatedby
     *
     * @return FullSix\ProjectForecastBundle\Entity\Users
     */
    public function getTaskscreatedby() {
        return $this->taskscreatedby;
    }

    public function __toString() {
        return (string) $this->getTasksname();
    }

    /**
     * Add taskstasksloads
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Tasksloads $taskstasksloads
     * @return Tasks
     */
    public function addTaskstasksload(\FullSix\ProjectForecastBundle\Entity\Tasksloads $taskstasksloads) {
        $this->taskstasksloads[] = $taskstasksloads;

        return $this;
    }

    /**
     * Remove taskstasksloads
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Tasksloads $taskstasksloads
     */
    public function removeTaskstasksload(\FullSix\ProjectForecastBundle\Entity\Tasksloads $taskstasksloads) {
        $this->taskstasksloads->removeElement($taskstasksloads);
    }

    /**
     * Get taskstasksloads
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTaskstasksloads() {
        return $this->taskstasksloads;
    }

    public function isIntervalValid(ExecutionContext $context)
    {
        if ($this->getTasksbeginat() > $this->getTasksendat()) {
            $context->addViolationAtSubPath('tasksbeginat', 'The start date cannot be later than the end date.');
            $context->addViolationAtSubPath('tasksendat', 'The end date cannot be before the start date.');
        }
    }

    /**
     * check if the entity can be (de)activated
     */
    public function checkActivation(){
        $err = array();

        if($this->getIsTrashed() == true){
            if ($this->getProjectsidprojects()->getIsTrashed() == true) {
                array_push($err, "project.task.cannotActivate.inactiveProject");
            }
        }else{
            //test for deactivation
        }

        return $err;
    }

    /*
     * (de)activate and compute side effects
     * */
    public function performActivationAndSideEffects(){
        //(de)activate
        $this->setIsTrashed(!$this->getIsTrashed());
        //side effects
        if($this->getIsTrashed()){
            //deactivate all tasksloads if the task was deactivated
            foreach ($this->taskstasksloads as $taskload) {
                $taskload->setIsTrashed(true);
            }
        }
    }

}
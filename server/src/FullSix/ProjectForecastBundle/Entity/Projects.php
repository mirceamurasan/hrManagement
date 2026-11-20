<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use FullSix\ProjectForecastBundle\Entity\NonDeletableEntity;
use FullSix\ProjectForecastBundle\Entity\KeyDates;

/**
 * FullSix\ProjectForecastBundle\Entity\Projects
 *
 * @ORM\Table(name="Projects")
 * @ORM\Entity(repositoryClass="FullSix\ProjectForecastBundle\Repository\ProjectsRepository")
 * @ORM\HasLifecycleCallbacks
 *
 * @GEDMO\Loggable(logEntryClass="FullSix\ProjectForecastBundle\Entity\PfLogEntries")
 *
 * @GRID\Source(columns="id, customersidcustomers.customersname, projectsname, projectfirsttasktdate, projectlasttasktdate, projectssuccess, projectsupdated, mergedWith.projectsname, isOutsourcing, isTrashed")
 */
class Projects extends NonDeletableEntity
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
     * @var string $projectsname
     *
     * @ORM\Column(name="ProjectsName", type="string", length=250, nullable=true)
     *
     * @GEDMO\Versioned
     *
     * @GRID\Column(title="Nom")
     */
    private $projectsname;

    /**
     * @var string $projectscomments
     *
     * @ORM\Column(name="ProjectsComments", type="text", nullable=true)
     *
     * @GEDMO\Versioned
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $projectscomments;

    /**
     * @var \DateTime $projectscreated
     *
     * @ORM\Column(name="ProjectsCreated", type="datetime", nullable=false)
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $projectscreated;

    /**
     * @var \DateTime $projectsupdated
     *
     * @ORM\Column(name="ProjectsUpdated", type="datetime", nullable=true)
     *
     * @GRID\Column(filterable=false, title="Last modification", format="d/m/Y")
     */
    private $projectsupdated;

    /**
     * @var Customers
     *
     * @ORM\ManyToOne(targetEntity="Customers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CustomersIdCustomers", referencedColumnName="Id")
     * })
     *
     * @GRID\Column(field="customersidcustomers.customersname", title="Customer", filterable=true)
     */
    private $customersidcustomers;

    /**
     * @var \DateTime $projectfirsttasktdate
     *
     * @ORM\Column(name="ProjectFirstTaskDate", type="datetime", nullable=true)
     *
     * @GRID\Column(filterable=false, title="Start date", format="d/m/Y")
     */
    private $projectfirsttasktdate;

    /**
     * @var \DateTime $projectlasttasktdate
     *
     * @ORM\Column(name="ProjectLastTaskDate", type="datetime", nullable=true)
     *
     * @GRID\Column(filterable=false, title="End date", format="d/m/Y")
     */
    private $projectlasttasktdate;

    /**
     * @var Login
     *
     * @ORM\ManyToOne(targetEntity="Login")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ProjectsCreatedBy", referencedColumnName="id")
     * })
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $projectscreatedby;

    /**
     *
     * @ORM\OneToMany(targetEntity="Tasks", mappedBy="projectsidprojects")
     *
     * @ORM\OrderBy({"tasksorder" = "ASC", "tasksbeginat" = "ASC"})
     */
    private $projectstasks;

     /**
     * @var integer $projectssuccess
     *
     * @ORM\Column(name="ProjectsSuccess", type="integer")
     *
     * @GRID\Column(title="Success", filterable=false)
     */
    private $projectssuccess;

    /**
     * @var boolean $isOutsourcing
     *
     * @ORM\Column(name="isOutsourcing", type="boolean", nullable=false)
     *
     * @GRID\Column(title="Outsoursing")
     */
    private $isOutsourcing;

    /**
     *
     * @ORM\OneToMany(targetEntity="ResourceProfileFlat", mappedBy="projectid")
     */
    protected $resourceprofilesflat;

    /**
     *
     * @ORM\OneToMany(targetEntity="KeyDates", mappedBy="projects")
     */
    private $keydates;


    /**
     * @var string $sapnumber
     *
     * @ORM\Column(name="SapNumber", type="string", length=50, nullable=true)
     *
     * @GRID\Column(title="SAP number")
     */
    protected $sapnumber;

    /**
     * @var string $infogeneral
     *
     * @ORM\Column(name="InfoGeneral", type="string", length=50, nullable=true)
     *
     * @GRID\Column(title="General Info")
     */
    protected $infogeneral;

    /**
     * @var string $infotech
     *
     * @ORM\Column(name="InfoTech", type="string", length=50, nullable=true)
     *
     * @GRID\Column(title="Technical Info")
     */
    protected $infotech;

    /**
     * @var $projectstatus
     *
     * @ORM\ManyToOne(targetEntity="ProjectStatuses", inversedBy="projects", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ProjectStatus", referencedColumnName="Id")
     * })
     *
     * @GRID\Column(title="Status", filterable=true)
     */
    protected $projectstatus;

    /**
     * @ORM\ManyToOne(targetEntity="Projects")
     * @ORM\JoinColumn(name="mergedWith", referencedColumnName="Id", nullable=true)
     * @GRID\Column(field="mergedWith.projectsname", title="common.field.mergedWith", filterable=true, operators={"like", "isNull", "isNotNull"})
     */
    protected $mergedWith;

    public function __construct() {
        $this->projectstasks = new ArrayCollection();
        $this->resourceprofilesflat = array();
        $this->keydates = array();
    }

    /**
     * Getter generique restraint aux champs possibles
     */
    public function get($input)
    {
        switch ($input) {
            case 'projectssuccess':
               return $this->getProjectssuccess();
               break;
           case 'projectfirsttasktdate':
               return $this->getProjectfirsttasktdate()->format('d-m-Y');
               break;
           case 'projectlasttasktdate':
               return $this->getProjectlasttasktdate()->format('d-m-Y');
               break;
           case 'customersidcustomers':
               return $this->getCustomersidcustomers()->getCustomersname();
               break;
           case 'projectsname':
               return $this->getProjectsname();
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
     * Set projectsname
     *
     * @param string $projectsname
     * @return Projects
     */
    public function setProjectsname($projectsname) {
        $this->projectsname = $projectsname;

        return $this;
    }

    /**
     * Get projectsname
     *
     * @return string
     */
    public function getProjectsname() {
        return $this->projectsname;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->projectsname;
    }

    /**
     * Set projectscomments
     *
     * @param string $projectscomments
     * @return Projects
     */
    public function setProjectscomments($projectscomments) {
        $this->projectscomments = $projectscomments;

        return $this;
    }

    /**
     * Get projectscomments
     *
     * @return string
     */
    public function getProjectscomments() {
        return $this->projectscomments;
    }

    /**
     * Set projectscreated
     *
     * @ORM\PrePersist
     * @return Projects
     */
    public function setProjectscreated() {
        $this->projectscreated = new \DateTime();

        return $this;
    }

    /**
     * Get projectscreated
     *
     * @return \DateTime
     */
    public function getProjectscreated() {
        return $this->projectscreated;
    }

    /**
     * Set projectsupdated
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @return Projects
     */
    public function setProjectsupdated() {
        $this->projectsupdated = new \DateTime();

        return $this;
    }

    /**
     * Get projectsupdated
     *
     * @return \DateTime
     */
    public function getProjectsupdated() {
        return $this->projectsupdated;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt() {
        return $this->projectsupdated;
    }

    /**
     * Set customersidcustomers
     *
     * @param FullSix\ProjectForecastBundle\Entity\Customers $customersidcustomers
     * @return Projects
     */
    public function setCustomersidcustomers(\FullSix\ProjectForecastBundle\Entity\Customers $customersidcustomers = null) {
        $this->customersidcustomers = $customersidcustomers;

        return $this;
    }

    /**
     * Get customersidcustomers
     *
     * @return FullSix\ProjectForecastBundle\Entity\Customers
     */
    public function getCustomersidcustomers() {
        return $this->customersidcustomers;
    }

    /**
     * Set projectstatus
     *
     * @param FullSix\ProjectForecastBundle\Entity\ProjectStatuses $projectstatus
     * @return Projects
     */
    public function setProjectstatus(\FullSix\ProjectForecastBundle\Entity\ProjectStatuses $projectstatus = null) {
        $this->projectstatus = $projectstatus;

        return $this;
    }

    /**
     * Get projectstatus
     *
     * @return FullSix\ProjectForecastBundle\Entity\ProjectStatuses
     */
    public function getProjectstatus() {
        return $this->projectstatus;
    }

    /**
     * Get customer
     *
     * @return FullSixProjectForecastBundle:Customers
     */
    public function getCustomer() {
        return $this->customersidcustomers;
    }

    /**
     * Set projectscreatedby
     *
     * @param FullSix\ProjectForecastBundle\Entity\Login $projectscreatedby
     * @return Projects
     */
    public function setProjectscreatedby(\FullSix\ProjectForecastBundle\Entity\Login $projectscreatedby = null) {
        $this->projectscreatedby = $projectscreatedby;

        return $this;
    }


    /**
     * Get projectscreatedby
     *
     * @return FullSix\ProjectForecastBundle\Entity\Login
     */
    public function getProjectscreatedby() {
        return $this->projectscreatedby;
    }

    public function __toString() {
        return (string) $this->getProjectsname();
    }

    /**
     * Get projectstasks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProjectstasks()
    {
        if (!is_array($this->projectstasks)) {
            return $this->projectstasks->toArray();
        } else {
            return $this->projectstasks;
        }
    }

    /**
     * Set projectssuccess
     *
     * @param integer $projectssuccess
     * @return Projects
     */
    public function setProjectssuccess($projectssuccess)
    {
        $this->projectssuccess = $projectssuccess;

        return $this;
    }

    /**
     * Get projectssuccess
     *
     * @return integer
     */
    public function getProjectssuccess()
    {
        return $this->projectssuccess;
    }

    /**
     * Set projectfirsttasktdate
     *
     * @param \DateTime $projectfirsttasktdate
     * @return Projects
     */
    public function setProjectfirsttasktdate($projectfirsttasktdate)
    {
        $this->projectfirsttasktdate = $projectfirsttasktdate;

        return $this;
    }

    /**
     * Get projectfirsttasktdate
     *
     * @return \DateTime
     */
    public function getProjectfirsttasktdate()
    {
        return $this->projectfirsttasktdate;
    }

    /**
     * Set projectlasttasktdate
     *
     * @param \DateTime $projectlasttasktdate
     * @return Projects
     */
    public function setProjectlasttasktdate($projectlasttasktdate)
    {
        $this->projectlasttasktdate = $projectlasttasktdate;

        return $this;
    }

    /**
     * Get projectlasttasktdate
     *
     * @return \DateTime
     */
    public function getProjectlasttasktdate()
    {
        return $this->projectlasttasktdate;
    }

    /**
     * Get projectlasttasktdate
     *
     * @return \DateTime
     */
    public function getLastTaskLoad()
    {
        return $this->projectlasttasktdate;
    }

    /**
     * Set isOutsourcing
     *
     * @param boolean $isOutsourcing
     * @return Projects
     */
    public function setIsOutsourcing($isOutsourcing)
    {
        $this->isOutsourcing = $isOutsourcing;

        return $this;
    }

    /**
     * Get isOutsourcing
     *
     * @return boolean
     */
    public function getIsOutsourcing()
    {
        return $this->isOutsourcing;
    }

    public function isOutsourcing()
    {
        $this->setIsOutsourcing(false);
        foreach ($this->getProjectstasks() as $task) {
            foreach ($task->getTaskstasksloads() as $tasksload) {
                if (!is_null($tasksload->getProvidersidproviders())) {
                    $this->setIsOutsourcing(true);
                }
            }
        }
    }

    public function getResourceprofilesflat()
    {
        if (!is_array($this->resourceprofilesflat)) {
            return $this->resourceprofilesflat->toArray();
        } else {
            return $this->resourceprofilesflat;
        }
    }
    /**
     * Get resourceprofilesflat
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function setResourceprofilesflat($resourceprofilesflat)
    {
        $this->resourceprofilesflat = $resourceprofilesflat;
    }

    /**
     * Get keydates
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getKeydates()
    {
        if (!is_array($this->keydates)) {
            return $this->keydates->toArray();
        } else {
            return $this->keydates;
        }
    }

    public function setKeydates($keydates)
    {
        $this->keydates = $keydates;
    }

    public function getSapnumber()
    {
        return $this->sapnumber;
    }

    public function getInfogeneral()
    {
        return $this->infogeneral;
    }

    public function getInfotech()
    {
        return $this->infotech;
    }

    public function getMergedWith()
    {
        return $this->mergedWith;
    }

    public function setSapnumber($sapnumber)
    {
        $this->sapnumber = $sapnumber;
    }

    public function setInfogeneral($infogeneral)
    {
        $this->infogeneral = $infogeneral;
    }

    public function setInfotech($infotech)
    {
        $this->infotech = $infotech;
    }

    public function setMergedWith($mergedWith)
    {
        $this->mergedWith = $mergedWith;
    }

    public function setProjectstasks($projectstasks)
    {
        $this->projectstasks = $projectstasks;
    }

    /**
     * check if the entity can be (de)activated
     */
    public function checkActivation(){
        $err = array();

        if($this->getIsTrashed() == true){
            //test for activation
            if($this->mergedWith != null){
                array_push($err, "project.cannotActivate.alreadyMearged");
            }

            //TODO : check active status if status!=null


            if ($this->getCustomersidcustomers()) {
                if ($this->getCustomersidcustomers()->getIsTrashed() == true) {
                    array_push($err, "project.cannotActivate.inactiveCustomer");
                }
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
            //deactivate all tasks and taskloads if the project was deactivated
            foreach ($this->projectstasks as $projectTask) {
                $projectTask->setIsTrashed(true);
                foreach ($projectTask->getTaskstasksloads() as $taskload) {
                    $taskload->setIsTrashed(true);
                }
            }
        }
    }

}
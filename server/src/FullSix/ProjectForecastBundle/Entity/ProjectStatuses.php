<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use FullSix\ProjectForecastBundle\Entity\NonDeletableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use FullSix\ProjectForecastBundle\Entity\Contracts;

/**
 * FullSix\ProjectForecastBundle\Entity\ProjectStatuses
 *
 * @ORM\Table(name="ProjectStatuses")
 * @ORM\Entity(repositoryClass="FullSix\ProjectForecastBundle\Repository\ProjectStatusesRepository")
 * @ORM\HasLifecycleCallbacks
 *
 * @UniqueEntity(fields="projectstatuseslabel",
 *     errorPath="",
 *     message=" There is already another status of the project with the same name.")
 *
 */
class ProjectStatuses extends NonDeletableEntity
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
     * @var string $projectstatuseslabel
     *
     * @ORM\Column(name="ProjectStatusesLabel", type="string", length=45, nullable=false)
     *
     * @GRID\Column(title="Label")
     */
    private $projectstatuseslabel;


    /**
     * @var \DateTime $projectstatusescreated
     *
     * @ORM\Column(name="ProjectStatusesCreated", type="datetime", nullable=false)
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $projectstatusescreated;

    /**
     * @var \DateTime $projectstatusesupdated
     *
     * @ORM\Column(name="ProjectStatusesUpdated", type="datetime", nullable=true)
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $projectstatusesupdated;


    /**
     * @var boolean $projectstatusesisfinal
     *
     * @ORM\Column(name="ProjectStatusesIsFinal", type="boolean", nullable=true)
     *
     * @GRID\Column(title="Finished")
     */
    private $projectstatusesisfinal;


    /**
     * @var \Taskloads
     *
     * @ORM\OneToMany(targetEntity="Projects", mappedBy="projectstatus")

     */
    protected $projects;

    public function __construct() {
        $this->projectstatusesisfinal = FALSE;
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
     * Set projectstatuseslabel
     *
     * @param string $projectstatuseslabel
     * @return ProjectStatuses
     */
    public function setProjectStatuseslabel($projectstatuseslabel) {
        $this->projectstatuseslabel = $projectstatuseslabel;

        return $this;
    }

    /**
     * Get projectstatuseslabel
     *
     * @return string
     */
    public function getProjectStatuseslabel() {
        return $this->projectstatuseslabel;
    }

    /**
     * Set projectstatusescreated
     *
     * @ORM\PrePersist
     * @return ProjectStatuses
     */
    public function setProjectStatusescreated() {
        $this->projectstatusescreated = new \DateTime();

        return $this;
    }

    /**
     * Get projectstatusescreated
     *
     * @return \DateTime
     */
    public function getProjectStatusescreated() {
        return $this->projectstatusescreated;
    }

    /**
     * Set projectstatusesupdated
     *
     * @ORM\PreUpdate
     * @return ProjectStatuses
     */
    public function setProjectStatusesupdated() {
        $this->projectstatusesupdated = new \DateTime();

        return $this;
    }

    /**
     * Get projectstatusesupdated
     *
     * @return \DateTime
     */
    public function getProjectStatusesupdated() {
        return $this->projectstatusesupdated;
    }


    /**
     * Get the finished state
     *
     * @return boolean
     */
    public function getProjectStatusesisfinal() {
        return $this->projectstatusesisfinal;
    }

    /**
     * Set state is a finished state
     *
     * @param boolean $projectstatusesisfinal
     * @return \FullSix\ProjectForecastBundle\Entity\ProjectStatuses
     */
    public function setProjectStatusesisfinal($projectstatusesisfinal) {
        $this->projectstatusesisfinal = $projectstatusesisfinal;

        return $this;
    }

    public function __toString() {
        return (string) $this->getProjectStatuseslabel();
    }

    public function getProjects()
    {
        return $this->projects;
    }

    public function setProjects($projects)
    {
        if (!is_array($this->projects)) {
            return $this->projects->toArray();
        } else {
            return $this->projects;
        }
    }

        public function checkActivation()
    {
        $err = array();

        if ($this->getIsTrashed() == true){
            //test for activation

        } else {
            //test for deactivation

            foreach ($this->getProjects() as $project) {
                 if ($project->getIsTrashed() == false) {
                    array_push($err, "admin.status.error.cannotDeactivate.activeProjects");
                    break;
                }
            }
        }

        return $err;
    }





}
<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use APY\DataGridBundle\Grid\Mapping as GRID;
use FullSix\ProjectForecastBundle\Entity\NonDeletableEntity;

/**
 * Workers
 *
 * @ORM\Table(name="Workers")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="FullSix\ProjectForecastBundle\Repository\WorkersRepository")
 * // never add * before a field name: eg: * startdate, ...
 * @GRID\Source(columns="user.usersfirstname, user.userslastname, user.login.username, contracttype.contracttypesname, employer.employersname, job.jobslabel, resourceprofilesflat.role, location.locationsname,
  startworkingdate, endworkingdate, active, team.id, id")
 */
class Workers
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Users", inversedBy="worker", cascade={"persist"})
     * @ORM\JoinColumn(name="WorkersUser", referencedColumnName="id")
     *
     * @GRID\Column(field="user.usersfirstname", title="Firstname")
     * @GRID\Column(field="user.userslastname", title="Lastname")
     * @GRID\Column(field="user.login.username", title="Username")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Locations")
     * @ORM\JoinColumn(name="WorkersLocation", referencedColumnName="id")
     * @GRID\Column(field="location.locationsname", title="admin.location.label")
     */
    protected $location;

    /**
     * Computed Field
     *
     * @var \DateTime $startWorkingDate
     *
     * @ORM\Column(name="StartWorkingDate", type="datetime", nullable=true)
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    protected $startworkingdate;

    /**
     * Computed Field
     *
     * @var \DateTime $endWorkingDate
     *
     * @ORM\Column(name="EndWorkingDate", type="datetime", nullable=true)
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    protected $endworkingdate;

    /**
     * @ORM\OneToMany(targetEntity="Contracts", mappedBy="idworker")
     * @GRID\Column(field="contracts.employer.employersname", title="admin.employer.label")
     */
    protected $contracts;

    /**
     * Computed Field
     *
     * @ORM\ManyToOne(targetEntity="ContractTypes")
     * @ORM\JoinColumn(name="WorkersContractType", referencedColumnName="id")
     *
     * @GRID\Column(field="contracttype.contracttypesname", title="admin.typeCon.label")
     */
    protected $contracttype;

    /**
     * Computed Field
     *
     * @ORM\ManyToOne(targetEntity="Employers")
     * @ORM\JoinColumn(name="WorkersEmployer", referencedColumnName="id")
     *
     * @GRID\Column(field="employer.employersname", title="Employer")
     */
    protected $employer;

    /**
     *
     * @ORM\OneToMany(targetEntity="ResourceProfileFlat", mappedBy="workerid")
     *
     * @GRID\Column(field="resourceprofilesflat.roles", title="Roles")
     */
    protected $resourceprofilesflat;

    /**
     *
     * @ORM\OneToMany(targetEntity="ResourceProfile", mappedBy="workerid")
     *
     */
    protected $resourceprofiles;

    /**
     * Computed Field
     *
     * @ORM\ManyToOne(targetEntity="Jobs")
     * @ORM\JoinColumn(name="WorkersJob", referencedColumnName="Id")
     * @GRID\Column(field="job.jobslabel", title="admin.job.label")
     */
    protected $job;

    /**
     * @ORM\ManyToOne(targetEntity="Teams", inversedBy="workers")
     * @ORM\JoinColumn(name="WorkersTeam", referencedColumnName="id")
     *
     * @GRID\Column(field="team.id", visible=false, filterable=false)
     */
    protected $team;

    /**
     * not persisted
     *
     * @GRID\Column(title="Active")
     */
    private $active;

    public function __construct()
    {
        $this->contracts = new ArrayCollection();
        $this->resourceprofilesflat = array();
        $this->resourceprofiles = array();
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get user
     *
     * @return \FullSix\ProjectForecastBundle\Entity\Users
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Providers $user
     * @return Workers
     */
    public function setUser(\FullSix\ProjectForecastBundle\Entity\Users $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get location
     *
     * @return \FullSix\ProjectForecastBundle\Entity\Locations
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set location
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Locations $location
     * @return Workers
     */
    public function setLocation(\FullSix\ProjectForecastBundle\Entity\Locations $location = null)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Set startWorkingDate
     *
     * @return Workers
     */
    public function setStartworkingdate($startworkingdate) {
        $this->startworkingdate = $startworkingdate;

        return $this;
    }

    /**
     * Get startWorkingDate
     *
     * @return \DateTime
     */
    public function getStartworkingdate() {
        return $this->startworkingdate;
    }

    /**
     * Set endWorkingDate
     *
     * @return Workers
     */
    public function setEndworkingdate($endworkingdate) {
        $this->endworkingdate = $endworkingdate;

        return $this;
    }

    /**
     * Get endWorkingDate
     *
     * @return \DateTime
     */
    public function getEndworkingdate() {
        return $this->endworkingdate;
    }

	/**
     * Add contracts
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Contracts $contracts
     * @return Workers
     */
    public function addContract(\FullSix\ProjectForecastBundle\Entity\Contracts $contracts)
    {
        $this->contracts[] = $contracts;

        return $this;
    }

    /**
     * Remove contracts
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Contracts $contracts
     */
    public function removeContract(\FullSix\ProjectForecastBundle\Entity\Contracts $contracts)
    {
        $this->contracts->removeElement($contracts);
    }

    /**
     * get contracts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getContracts()
    {
        return $this->contracts->toArray();
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

    public function getResourceprofiles()
    {
        if (!is_array($this->resourceprofiles)) {
            return $this->resourceprofiles->toArray();
        } else {
            return $this->resourceprofiles;
        }
    }
    /**
     * Get resourceprofilesflat
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function setResourceprofiles($resourceprofiles)
    {
        $this->resourceprofiles = $resourceprofiles;
    }

    /**
     * Set job
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Jobs $job
     * @return Workers
     */
    public function setJob(\FullSix\ProjectForecastBundle\Entity\Jobs $job = null)
    {
        $this->job = $job;

        return $this;
    }

    /**
     * Get job
     *
     * @return \FullSix\ProjectForecastBundle\Entity\Jobs
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * Set contracttype
     *
     * @param \FullSix\ProjectForecastBundle\Entity\ContractTypes $contracttype
     * @return Workers
     */
    public function setContracttype(\FullSix\ProjectForecastBundle\Entity\Contracttypes $contracttype = null)
    {
        $this->contracttype = $contracttype;

        return $this;
    }

    /**
     * Get contracttype
     *
     * @return \FullSix\ProjectForecastBundle\Entity\ContractTypes
     */
    public function getContracttype()
    {
        return $this->contracttype;
    }

    /**
     * Set employer
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Employers $employer
     * @return Workers
     */
    public function setEmployer(\FullSix\ProjectForecastBundle\Entity\Employers $employer = null)
    {
        $this->employer = $employer;

        return $this;
    }

    /**
     * Get employer
     *
     * @return \FullSix\ProjectForecastBundle\Entity\Employers
     */
    public function getEmployer()
    {
        return $this->employer;
    }

    /**
     * Get active
     *
     * @return \string
     */
    public function getActive() {

        $today = new \DateTime();
        if(!empty($this->startworkingdate) &&$this->startworkingdate <= $today
            && (empty($this->endworkingdate) || $this->endworkingdate >= $today)){
            $this->active = true;
        }else{
            $this->active = false;
        }
        return $this->active;
    }

    /**
     * Get team
     *
     * @return \FullSix\ProjectForecastBundle\Entity\Teams
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set team
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Teams $team
     * @return Workers
     */
    public function setTeam(\FullSix\ProjectForecastBundle\Entity\Teams $team = null)
    {
        $this->team = $team;

        return $this;
    }

    public function __toString()
    {
        return $this->getUser()->getCheckedName();
    }

    public function getPeriodContainingVacation($vacation){
        foreach ($this->getContracts() as $workerContract) {
            foreach ($workerContract->getPeriods() as $workerPeriod) {
                if($workerPeriod->getStartdate() <= $vacation->getStartdate() &&
                    (!$workerPeriod->getEnddate() || $workerPeriod->getEnddate() >= $vacation->getEnddate() )){
                    //end date is null or grater then vacation's end date
                    return $workerPeriod;
                }
            }
        }
        return null;
    }
}

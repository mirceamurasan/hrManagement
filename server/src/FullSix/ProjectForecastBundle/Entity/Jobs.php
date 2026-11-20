<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use FullSix\ProjectForecastBundle\Entity\NonDeletableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * FullSix\ProjectForecastBundle\Entity\Jobs
 *
 * @ORM\Table(name="Jobs")
 * @ORM\Entity(repositoryClass="FullSix\ProjectForecastBundle\Repository\JobsRepository")
 * @ORM\HasLifecycleCallbacks
 *
 * @UniqueEntity(fields="jobslabel",
 *     errorPath="",
 *     message=" Il existe déjà un autre métier avec le même nom.")
 *
 */
class Jobs extends NonDeletableEntity
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
     * @var string $jobslabel
     *
     * @ORM\Column(name="JobsLabel", type="string", length=45, nullable=false)
     *
     * @GRID\Column(title="common.field.label")
     */
    private $jobslabel;

    /**
     * @var \DateTime $jobscreated
     *
     * @ORM\Column(name="JobsCreated", type="datetime", nullable=false)
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $jobscreated;

    /**
     * @var \DateTime $jobsupdated
     *
     * @ORM\Column(name="JobsUpdated", type="datetime", nullable=true)
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $jobsupdated;

    /**
     * @var \Contracts
     *
     * @ORM\OneToMany(targetEntity="Contracts", mappedBy="idjob")
     */
    protected $contracts;

    /**
     * @var \Taskloads
     *
     * @ORM\OneToMany(targetEntity="Tasksloads", mappedBy="jobsidjobs")
     */
    protected $taskloads;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set jobslabel
     *
     * @param string $jobslabel
     * @return Jobs
     */
    public function setJobslabel($jobslabel) {
        $this->jobslabel = $jobslabel;

        return $this;
    }

    /**
     * Get jobslabel
     *
     * @return string
     */
    public function getJobslabel() {
        return $this->jobslabel;
    }

    /**
     * Get Label
     *
     * @return string
     */
    public function getLabel() {
        return $this->jobslabel;
    }

    /**
     * Set jobscreated
     *
     * @ORM\PrePersist
     * @return Jobs
     */
    public function setJobscreated() {
        $this->jobscreated = new \DateTime();

        return $this;
    }

    /**
     * Get jobscreated
     *
     * @return \DateTime
     */
    public function getJobscreated() {
        return $this->jobscreated;
    }

    /**
     * Set jobsupdated
     *
     * @ORM\PreUpdate
     * @return Jobs
     */
    public function setJobsupdated() {
        $this->jobsupdated = new \DateTime();

        return $this;
    }

    /**
     * Get jobsupdated
     *
     * @return \DateTime
     */
    public function getJobsupdated() {
        return $this->jobsupdated;
    }

    public function __toString() {
        return (string) $this->getJobslabel();
    }

    public function getContracts()
    {
        if (!is_array($this->contracts)) {
            return $this->contracts->toArray();
        } else {
            return $this->contracts;
        }
    }

    public function setContracts($contracts)
    {
        $this->contracts = $contracts;
    }

    public function getTaskloads()
    {
        if (!is_array($this->taskloads)) {
            return $this->taskloads->toArray();
        } else {
            return $this->taskloads;
        }
    }

    public function setTaskloads($taskloads)
    {
        $this->taskloads = $taskloads;
    }


    public function checkActivation()
    {
        $err = array();
        $now = new \DateTime();

        if ($this->getIsTrashed() == true){
//            //test for activation

        } else {
            //test for deactivation TODO
           /* foreach ($this->getPeriods() as $period) {
                if ($period->getEnddate() > $now) {
                    array_push($err, "admin.jobs.error.cannotDeactivate.futurePeriods");
                    break;
                }
            }*/

            foreach ($this->getTaskloads() as $taskload) {
                 if ($taskload->getIsTrashed() == false) {
                    array_push($err, "admin.agency.error.cannotDeactivate.activeTaskloads");
                    break;
                }
            }
        }

        return $err;
    }



}
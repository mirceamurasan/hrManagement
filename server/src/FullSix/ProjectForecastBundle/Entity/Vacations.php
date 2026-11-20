<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use APY\DataGridBundle\Grid\Mapping as GRID;
use FullSix\ProjectForecastBundle\Entity\NonDeletableEntity;

/**
 * Vacations
 *
 * @ORM\Table(name="Vacations")
 * @ORM\Entity(repositoryClass="FullSix\ProjectForecastBundle\Repository\VacationsRepository")
 * @ORM\HasLifecycleCallbacks
 *
 * @GRID\Source(columns="vacationperiods.idcontract.idworker.id, startdate, enddate, morning, afternoon, isTrashed, id")
 *
 */
class Vacations extends NonDeletableEntity
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
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startdate", type="datetime", nullable=false)
     *
     * @GRID\Column(title="common.field.startDate")
     */
    protected $startdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="enddate", type="datetime", nullable=false)
     *
     * @GRID\Column(title="common.field.endDate")
     */
    protected $enddate;

    /**
     *
     * @var boolean
     *
     * @ORM\Column(name="afternoon", type="boolean")
     *
     * @GRID\Column(title="worker.vacation.afternoon")
     */
    protected $afternoon;

    /**
     *
     * @var boolean
     *
     * @ORM\Column(name="morning", type="boolean")
     *
     * @GRID\Column(title="worker.vacation.morning")
     */
    protected $morning;

    /**
     *
     * Computed field
     *
     * @var float
     *
     * @ORM\Column(name="coef", type="decimal", precision=2, scale=1)
     *
     */
    protected $coef;

    /**
     * @ORM\ManyToOne(targetEntity="Periods", inversedBy="periodvacations")
     * @ORM\JoinColumn(name="VacationPeriods", referencedColumnName="id")
     *
     * @GRID\Column(field="vacationperiods.idcontract.idworker.id", title="id", visible=false, filterable=false)
     */
    protected $vacationperiods;

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
     * Set startdate
     *
     * @param \DateTime $startdate
     * @return Periods
     */
    public function setStartdate($startdate)
    {
        $this->startdate = $startdate;

        return $this;
    }

    /**
     * Get startdate
     *
     * @return \DateTime
     */
    public function getStartdate()
    {
        return $this->startdate;
    }

    /**
     * Set enddate
     *
     * @param \DateTime $enddate
     * @return Periods
     */
    public function setEnddate($enddate)
    {
        $this->enddate = $enddate;

        return $this;
    }

    /**
     * Get enddate
     *
     * @return \DateTime
     */
    public function getEnddate()
    {
        return $this->enddate;
    }

    /**
     * Set afternoon
     *
     * @param boolean $afternoon
     *
     * @return Vacations
     */
    public function setAfternoon($afternoon)
    {
        $this->afternoon = $afternoon;

        return $this;
    }

 	/**
     * Get afternoon
     *
     * @return boolean
     */

    public function getAfternoon()
    {
        return $this->afternoon;
    }

    /**
     * Set morning
     *
     * @param boolean $morning
     *
     * @return Vacations
     */
    public function setMorning($morning)
    {
        $this->morning = $morning;

        return $this;
    }

 	/**
     * Get morning
     *
     * @return boolean
     */
    public function getMorning()
    {
        return $this->morning;
    }

    /**
     * Set coef
     *
     * @param decimal $coef
     *
     * @return Vacations
     */
    public function setCoef($coef)
    {
        $this->coef = $coef;

        return $this;
    }

 	/**
     * Get coef
     *
     * @return decimal
     */

    public function getCoef()
    {
        return $this->coef;
    }

	/**
     * Get vacationperiods
     *
     * @return Periods
     */
    public function getVacationperiods()
    {
        return $this->vacationperiods;
    }
    /**
     * Get vacationperiods
     *
     * @return Vacations
     */
    public function setVacationperiods($vacationperiods)
    {
        $this->vacationperiods = $vacationperiods;
    }

    public function getTypeOfVacationAsString()
    {
        if ($this->coef != 1.0) {
            return $this->afternoon ? 'afternoon' : 'morning';
        }
        return 'fullday';
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function computeCoef()
    {
        if($this->morning && $this->afternoon){
            $this->coef = 1.0;
        }else if(!$this->morning && !$this->afternoon){
            $this->coef = 0.0;
        }else{
            $this->coef = 0.5;
        }
    }

    /*
     * not persited
     * used for vacation validation
     * */
    private $worker;

    public function getWorker()
    {
        return $this->worker;
    }

    public function setWorker($worker)
    {
        $this->worker = $worker;
    }

    public function checkActivation()
    {
        $err = array();

        if($this->getIsTrashed() == true){
            //test for activation
            if($this->vacationperiods->getIsTrashed()){
                array_push($err, "worker.vacation.cannotActivate.inactivePeriod");
            }
            /* A voir
            if($this->vacationperiods->getServiceprovider()){
                array_push($err, "Service providers cannot have vacations");
            }*/
        }else{
            //test for deactivation
        }

        return $err;
    }

    public function containsDay($day){
        if($this->startdate <= $day && $this->enddate >= $day){
            return true;
        }
        return false;
    }

}
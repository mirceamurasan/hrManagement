<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Periods
 *
 * @ORM\Table(name="Periods")
 * @ORM\Entity(repositoryClass="FullSix\ProjectForecastBundle\Repository\PeriodsRepository")
 * @ORM\HasLifecycleCallbacks
 * @GRID\Source(columns="idcontract.id, id, startdate, enddate, tjm, isTrashed")
 */
class Periods extends NonDeletableEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @GRID\Column(title="Number")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startdate", type="datetime", nullable=false)
     *
     * @GRID\Column(title="common.field.startDate")
     */
    private $startdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="enddate", type="datetime", nullable=true)
     *
     * @GRID\Column(title="common.field.endDate")
     */
    private $enddate;

    /**
     * @var integer
     *
     * @ORM\Column(name="tjm", type="integer", nullable=true)
     *
     * @GRID\Column(title="Tax Journalier")
     */
    private $tjm;

    /**
     * @var \Contracts
     *
     * @ORM\ManyToOne(targetEntity="Contracts", inversedBy="periods")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idcontract", referencedColumnName="id", nullable=false)
     * })
     * @GRID\Column(field="idcontract.id", title="id", visible=false, filterable=false)
     */
    private $idcontract;



    /**
     * Computed Field
     *
     * @var boolean
     *
     * @ORM\Column(name="serviceProvider", type="boolean")
     *
     */
    private $serviceprovider;

    /**
     * @ORM\OneToMany(targetEntity="Vacations", mappedBy="vacationperiods")
     *
     */
    private $periodvacations;


    public function __construct()
    {
        $this->periodvacations = new ArrayCollection();
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
     * Set tjm
     *
     * @param integer $tjm
     * @return Periods
     */
    public function setTjm($tjm)
    {
        $this->tjm = $tjm;

        return $this;
    }

    /**
     * Get tjm
     *
     * @return boolean
     */
    public function getTjm()
    {
        return $this->tjm;
    }

    /**
     * Set idcontract
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Contracts $idcontract
     * @return Periods
     */
    public function setIdcontract(\FullSix\ProjectForecastBundle\Entity\Contracts $idcontract = null)
    {
        $this->idcontract = $idcontract;

        return $this;
    }

    /**
     * Get idcontract
     *
     * @return \FullSix\ProjectForecastBundle\Entity\Contracts
     */
    public function getIdcontract()
    {
        return $this->idcontract;
    }

    /**
     * Set serviceprovider
     *
     * @param boolean $serviceprovider
     *
     * @return NonDeletableEntity
     */
    public function setServiceprovider($serviceprovider)
    {
        $this->serviceprovider = $serviceprovider;

        return $this;
    }

    /**
     * Get serviceprovider
     *
     * @return boolean
     */

    public function getServiceprovider()
    {
        return $this->serviceprovider;
    }

   /**
    * @ORM\PrePersist
    * @ORM\PreUpdate
    */
    public function computeServiceProvider()
    {
        if($this->getIdContract()){
            $this->serviceprovider = $this->getIdContract()->getEmployer()->getAgenciesidagencies() ? false : true;
        }else{
            $this->serviceprovider = false;
        }
    }

    /**
     * Add periodvacations
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Vacations $periodvacations
     * @return Periods
     */
    public function addPeriodvacations(\FullSix\ProjectForecastBundle\Entity\Vacations $periodvacations)
    {
        $this->periodvacations[] = $periodvacations;

        return $this;
    }

    /**
     * Remove periodvacations
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Vacations $periodvacations
     */
    public function removePeriodvacations(\FullSix\ProjectForecastBundle\Entity\Vacations $periodvacations)
    {
        $this->periodvacations->removeElement($periodvacations);
    }

    /**
     * get periodvacations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPeriodvacations()
    {
        return $this->periodvacations->toArray();
    }

    /**
     * check if the entity can be (de)activated
     */
    public function checkActivation(){
        $err = array();

        if($this->getIsTrashed() == true){
            if ($this->getIdcontract()->getIsTrashed()) {
                array_push($err, "worker.contract.period.cannotActivate.inactiveContract");
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
            //deactivate all vacations
            foreach ($this->getPeriodvacations() as $vacation) {
                $vacation->setIsTrashed(true);
            }
        }
    }

}

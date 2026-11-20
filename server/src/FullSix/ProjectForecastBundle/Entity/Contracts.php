<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * Contracts
 *
 * @ORM\Table(name="Contracts")
 * @ORM\Entity(repositoryClass="FullSix\ProjectForecastBundle\Repository\ContractsRepository")
 * @ORM\HasLifecycleCallbacks
 * @GRID\Source(columns="idworker.id, bdc, contracttype.contracttypesname, employer.employersname, idjob.jobslabel, isTrashed, periods:size, id")
 */
class Contracts extends NonDeletableEntity
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
     * @var \ContractTypes
     *
     * @ORM\ManyToOne(targetEntity="ContractTypes", inversedBy="contracts", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contract_type_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * })
     *
     * @GRID\Column(field="contracttype.contracttypesname", title="admin.typeCon.label")
     */
    private $contracttype;

    /**
     * @var \Employers
     *
     * @ORM\ManyToOne(targetEntity="Employers", inversedBy="contracts", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="employer_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * })
     *
     * @GRID\Column(field="employer.employersname", title="admin.employer.label")
     */
    private $employer;

    /**
     * @var integer
     *
     */
    private $bdc;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=true)
     *
     * @GRID\Column(title="Comment")
     */
    private $comment;

    /**
     * @var \Workers
     *
     * @ORM\ManyToOne(targetEntity="Workers", inversedBy="contracts", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idworker", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * })
     *
     * @GRID\Column(field="idworker.id", title="id", visible=false, filterable=false)
     */
    private $idworker;

    /**
     * @ORM\OneToMany(targetEntity="Periods", mappedBy="idcontract", cascade={"all"})
     *
     * @GRID\Column(field="periods:size", title="No. de périodes", filterable=false)
     */
    private $periods;

    /**
     * @var \Jobs
     *
     * @ORM\ManyToOne(targetEntity="Jobs", inversedBy="periods", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idjob", referencedColumnName="Id", nullable=false)
     * })
     *
     * @GRID\Column(field="idjob.jobslabel", title="admin.job.label")
     */
    private $idjob;

    /**
     * Set idjob
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Jobs $idjob
     * @return Periods
     */
    public function setIdjob(\FullSix\ProjectForecastBundle\Entity\Jobs $idjob = null)
    {
        $this->idjob = $idjob;

        return $this;
    }

    /**
     * Get idjob
     *
     * @return \FullSix\ProjectForecastBundle\Entity\Jobs
     */
    public function getIdjob()
    {
        return $this->idjob;
    }


	public function __construct()
    {
		$this->periods = new ArrayCollection();
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
     * Set bdc
     *
     * @param integer $bdc
     * @return Contracts
     */
    public function setBdc($bdc)
    {
        $this->bdc = $bdc;

        return $this;
    }

    /**
     * Get bdc
     *
     * @return integer
     */
    public function getBdc()
    {
        return $this->bdc;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return Contracts
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set idworker
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Workers $idworker
     * @return Contracts
     */
    public function setIdworker(\FullSix\ProjectForecastBundle\Entity\Workers $idworker)
    {
        $this->idworker = $idworker;

        return $this;
    }

    /**
     * Get idworker
     *
     * @return \FullSix\ProjectForecastBundle\Entity\Workers
     */
    public function getIdworker()
    {
        return $this->idworker;
    }

    /**
     * Add periods
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Periods $periods
     * @return Contracts
     */
    public function addPeriod(\FullSix\ProjectForecastBundle\Entity\Periods $periods)
    {
        $this->periods[] = $periods;
        $periods->setIdcontract($this);

        return $this;
    }

    /**
     * Remove periods
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Periods $periods
     */
    public function removePeriod(\FullSix\ProjectForecastBundle\Entity\Periods $periods)
    {
        $this->periods->removeElement($periods);
    }

    /**
     * Get periods
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPeriods()
    {
        return $this->periods;
    }

    /**
     * set contracttype
     *
     * @param \FullSix\ProjectForecastBundle\Entity\ContractTypes $contracttype
     */
    public function setContracttype(\FullSix\ProjectForecastBundle\Entity\ContractTypes $contracttype)
    {
        $this->contracttype = $contracttype;
    }

    /**
     * Get contracttype
     *
     * @return \FullSix\ProjectForecastBundle\Entity\Contracttypes
     */
    public function getContracttype()
    {
        return $this->contracttype;
    }

    /**
     * set employer
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Employers $employer
     */
    public function setEmployer(\FullSix\ProjectForecastBundle\Entity\Employers $employer)
    {
        $this->employer = $employer;
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
     * check if the entity can be (de)activated
     */
    public function checkActivation(){
        $err = array();

        if($this->getIsTrashed() == true){
            if ($this->getEmployer()->getIsTrashed()) {
                array_push($err, "worker.contract.cannotActivate.inactiveEmployer");
            }
            if ($this->getContracttype()->getIsTrashed()) {
                array_push($err, "worker.contract.cannotActivate.inactiveContractType");
            }
            if ($this->getIdjob()->getIsTrashed()) {
                array_push($err, "worker.contract.period.cannotActivate.inactiveJob");
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
            //deactivate all contract periods and it's vacations
            foreach ($this->periods as $period) {
                $period->setIsTrashed(true);
                foreach ($period->getPeriodvacations() as $vacation) {
                    $vacation->setIsTrashed(true);
                }
            }
        }
    }
}

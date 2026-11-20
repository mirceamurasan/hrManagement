<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use FullSix\ProjectForecastBundle\Entity\NonDeletableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * FullSix\ProjectForecastBundle\Entity\Employers
 *
 * @ORM\Table(name="Employers")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields="employersname",
 *     errorPath="",
 *     message=" Il existe déjà un autre employeur avec le même nom.")
 *
 */
class Employers extends NonDeletableEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="EmployersName", type="string", length=50, nullable=false, unique=true)
     *
     * @GRID\Column(title="common.field.name")
     */
    private $employersname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="EmployersCreated", type="datetime", nullable=false)
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $employerscreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="EmployersUpdated", type="datetime", nullable=true)
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $employersupdated;

    /**
     * @var Agencies
     *
     * @ORM\ManyToOne(targetEntity="Agencies", inversedBy="employers", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="AgenciesIdAgencies", referencedColumnName="Id", nullable=true)
     * })
     * @GRID\Column(field="agenciesidagencies.agenciesname", title="Agency", filterable=true, operatorsVisible=false, filter="select")
     */
    private $agenciesidagencies;

    /**
     *
     * @ORM\OneToMany(targetEntity="Contracts", mappedBy="employer")
     */
    protected $contracts;


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
     * Set employersname
     *
     * @param string $employersname
     *
     * @return Employers
     */
    public function setEmployersname($employersname)
    {
        $this->employersname = $employersname;

        return $this;
    }

    /**
     * Get employersname
     *
     * @return string
     */
    public function getEmployersname()
    {
        return $this->employersname;
    }

    /**
     * Set employerscreated
     *
     * @ORM\PrePersist
     *
     * @return Employers
     */
    public function setEmployerscreated($employerscreated)
    {
        $this->employerscreated = new \DateTime();

        return $this;
    }

    /**
     * Get employerscreated
     *
     * @return \DateTime
     */
    public function getEmployerscreated()
    {
        return $this->employerscreated;
    }

    /**
     * Set employersupdated
     *
     * @ORM\PreUpdate
     *
     * @return Employers
     */
    public function setEmployersupdated($employersupdated)
    {
        $this->employersupdated = new \DateTime();

        return $this;
    }

    /**
     * Get employersupdated
     *
     * @return \DateTime
     */
    public function getEmployersupdated()
    {
        return $this->employersupdated;
    }

    /**
     * Set agenciesidagencies
     *
     * @param FullSix\ProjectForecastBundle\Entity\Agencies $agenciesidagencies
     * @return Employers
     */
    public function setAgenciesidagencies(\FullSix\ProjectForecastBundle\Entity\Agencies $agenciesidagencies = null)
    {
        $this->agenciesidagencies = $agenciesidagencies;

        return $this;
    }

    /**
     * Get agenciesidagencies
     *
     * @return FullSix\ProjectForecastBundle\Entity\Agencies
     */
    public function getAgenciesidagencies()
    {
        return $this->agenciesidagencies;
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


    public function checkActivation() {
        $err = array();

        if ($this->getIsTrashed() == true) {
            //test for activation
            if ($this->getAgenciesidagencies()) {
                if ($this->getAgenciesidagencies()->getIsTrashed() == true) {
                    array_push($err, "admin.employer.cannotActivate.inactiveAgency");
                }
            }
        } else {
            //test for deactivation
            foreach ($this->getContracts() as $contract) {
                // verify if the contract has future periods
                // array_push($err, "admin.employer.cannotDectivate.noContracts");
            }

        }
        return $err;
    }

}
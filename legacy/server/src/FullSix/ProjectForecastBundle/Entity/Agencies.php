<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use FullSix\ProjectForecastBundle\Entity\NonDeletableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * FullSix\ProjectForecastBundle\Entity\Agencies
 *
 * @ORM\Table(name="Agencies")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 *
 * @UniqueEntity(fields="agenciesname",
 *     errorPath="",
 *     message="Il existe déjà une autre agence avec le même nom.")
 *
 */
class Agencies extends NonDeletableEntity
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
     * @var string $agenciesname
     *
     * @ORM\Column(name="AgenciesName", type="string", length=45, nullable=false)
     *
     * @GRID\Column(title="common.field.name")
     */
    private $agenciesname;

    /**
     * @var \DateTime $agenciescreated
     *
     * @ORM\Column(name="AgenciesCreated", type="datetime", nullable=false)
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $agenciescreated;

    /**
     * @var \DateTime $agenciesupdated
     *
     * @ORM\Column(name="AgenciesUpdated", type="datetime", nullable=true)
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $agenciesupdated;

    /**
     * @var Login
     *
     * @ORM\ManyToOne(targetEntity="Login")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="AgenciesCreatedBy", referencedColumnName="id")
     * })
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $agenciescreatedby;

    /**
     * @var Login
     *
     * @ORM\ManyToOne(targetEntity="Login")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="AgenciesUpdatedBy", referencedColumnName="id")
     * })
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $agenciesupdatedby;

    /**
     *
     * @ORM\OneToMany(targetEntity="Customers", mappedBy="agenciesidagencies")
     */
    protected $customers;
    /**
     *
     * @ORM\OneToMany(targetEntity="Employers", mappedBy="agenciesidagencies")
     */
    protected $employers;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set agenciesname
     *
     * @param string $agenciesname
     * @return Agencies
     */
    public function setAgenciesname($agenciesname) {
        $this->agenciesname = $agenciesname;

        return $this;
    }

    /**
     * Get agenciesname
     *
     * @return string
     */
    public function getAgenciesname() {
        return $this->agenciesname;
    }

    /**
     * Set agenciescreated
     *
     * @ORM\PrePersist
     *
     * @return Agencies
     */
    public function setAgenciescreated() {
        $this->agenciescreated = new \DateTime();

        return $this;
    }

    /**
     * Get agenciescreated
     *
     * @return \DateTime
     */
    public function getAgenciescreated() {
        return $this->agenciescreated;
    }

    /**
     * Set agenciesupdated
     *
     * @ORM\PreUpdate
     * @return Agencies
     */
    public function setAgenciesupdated() {
        $this->agenciesupdated = new \DateTime();

        return $this;
    }

    /**
     * Get agenciesupdated
     *
     * @return \DateTime
     */
    public function getAgenciesupdated() {
        return $this->agenciesupdated;
    }

    /**
     * Set agenciescreatedby
     *
     * @param FullSix\ProjectForecastBundle\Entity\Login $agenciescreatedby
     * @return Agencies
     */
    public function setAgenciescreatedby(\FullSix\ProjectForecastBundle\Entity\Login $agenciescreatedby = null) {
        $this->agenciescreatedby = $agenciescreatedby;

        return $this;
    }

    /**
     * Get agenciescreatedby
     *
     * @return FullSix\ProjectForecastBundle\Entity\Login
     */
    public function getAgenciescreatedby() {
        return $this->agenciescreatedby;
    }

    /**
     * Get updated By
     *
     * @return FullSix\ProjectForecastBundle\Entity\Login
     */
    public function getAgenciesupdatedby() {
        return $this->agenciesupdatedby;
    }

    /**
     * Set Updated By
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Login $agenciesupdatedby
     * @return \FullSix\ProjectForecastBundle\Entity\Agencies
     */
    public function setAgenciesupdatedby(\FullSix\ProjectForecastBundle\Entity\Login $agenciesupdatedby = null) {

        $this->agenciesupdatedby = $agenciesupdatedby;

        return $this;
    }


    public function __toString() {
        return (string) $this->getAgenciesname();
    }

    public function getCustomers()
    {
        if (!is_array($this->customers)) {
            return $this->customers->toArray();
        } else {
            return $this->customers;
        }
    }

    public function setCustomers($customers)
    {
        $this->customers = $customers;
    }

    public function getEmployers()
    {
        if (!is_array($this->employers)) {
            return $this->employers->toArray();
        } else {
            return $this->employers;
        }
    }

    public function setEmployers($employers)
    {
        $this->employers = $employers;
    }



    public function checkActivation()
    {
        $err = array();

        if($this->getIsTrashed() == true){
//            //test for activation

        }else{
            //test for deactivation

            foreach ($this->getCustomers() as $customer) {
                if ($customer->getIsTrashed() == false) {
                    array_push($err, "admin.agency.error.cannotDeactivate.activeCustomers");
                    break;
                }
            }

            foreach ($this->getEmployers() as $employer) {
                if ($employer->getIsTrashed() == false) {
                    array_push($err, "admin.agency.error.cannotDeactivate.activeEmployers");
                    break;
                }
            }
        }

        return $err;
    }

}
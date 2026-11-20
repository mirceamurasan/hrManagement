<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use FullSix\ProjectForecastBundle\Entity\NonDeletableEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * FullSix\ProjectForecastBundle\Entity\Customers
 *
 * @ORM\Table(name="Customers")
 * @ORM\Entity(repositoryClass="FullSix\ProjectForecastBundle\Repository\CustomersRepository")
 * @ORM\HasLifecycleCallbacks
 *
 * @UniqueEntity(fields="customersname",
 *     errorPath="",
 *     message=" Il existe déjà un autre client avec le même nom.")
 *
 */
class Customers extends NonDeletableEntity {

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
     * @var string $customersname
     *
     * @ORM\Column(name="CustomersName", type="string", length=50, nullable=true)
     *
     * @GRID\Column(title="Name")
     */
    private $customersname;

    /**
     * @var \DateTime $customerscreated
     *
     * @ORM\Column(name="CustomersCreated", type="datetime", nullable=false)
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $customerscreated;

    /**
     * @var \DateTime $customersupdated
     *
     * @ORM\Column(name="CustomersUpdated", type="datetime", nullable=true)
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $customersupdated;

    /**
     * @var Agencies
     *
     * @ORM\ManyToOne(targetEntity="Agencies", inversedBy="customers", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="AgenciesIdAgencies", referencedColumnName="Id")
     * })
     * @GRID\Column(field="agenciesidagencies.agenciesname", title="Agency", filterable=true, operatorsVisible=false, filter="select")
     */
    private $agenciesidagencies;

    /**
     * @var Login
     *
     * @ORM\ManyToOne(targetEntity="Login")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CustomersCreatedBy", referencedColumnName="id")
     * })
     */
    private $customerscreatedby;


    /**
     *
     * @ORM\OneToMany(targetEntity="CustomersSapIds", mappedBy="customers")
     */
    private $sapids;


    /**
     * @ORM\ManyToOne(targetEntity="Customers")
     * @ORM\JoinColumn(name="mergedWith", referencedColumnName="Id", nullable=true)
     * @GRID\Column(field="mergedWith.customersname", title="common.field.mergedWith", filterable=true, operators={"like", "isNull", "isNotNull"})
     */
    private $mergedWith;


     /**
     *
     * @ORM\OneToMany(targetEntity="ResourceProfile", mappedBy="customerid")
     */
    protected $resourceprofiles;

     /**
     *
     * @ORM\OneToMany(targetEntity="Projects", mappedBy="customersidcustomers")
     */
    protected $projects;

    public function __construct() {
        $this->mergedWith = null;
        $this->sapids = array();
        $this->resourceprofiles = array();
        $this->projects = array();
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
     * Set customersname
     *
     * @param string $customersname
     * @return Customers
     */
    public function setCustomersname($customersname) {
        $this->customersname = $customersname;

        return $this;
    }

    /**
     * Get customersname
     *
     * @return string
     */
    public function getCustomersname() {
        return $this->customersname;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->customersname;
    }

    /**
     * Set customerscreated
     *
     * @ORM\PrePersist
     * @return Customers
     */
    public function setCustomerscreated() {
        $this->customerscreated = new \DateTime();

        return $this;
    }

    /**
     * Get customerscreated
     *
     * @return \DateTime
     */
    public function getCustomerscreated() {
        return $this->customerscreated;
    }

    /**
     * Set customersupdated
     *
     * @ORM\PreUpdate
     * @return Customers
     */
    public function setCustomersupdated() {
        $this->customersupdated = new \DateTime();

        return $this;
    }

    /**
     * Get customersupdated
     *
     * @return \DateTime
     */
    public function getCustomersupdated() {
        return $this->customersupdated;
    }

    /**
     * Set agenciesidagencies
     *
     * @param FullSix\ProjectForecastBundle\Entity\Agencies $agenciesidagencies
     * @return Customers
     */
    public function setAgenciesidagencies(\FullSix\ProjectForecastBundle\Entity\Agencies $agenciesidagencies = null) {
        $this->agenciesidagencies = $agenciesidagencies;

        return $this;
    }

    /**
     * Get agenciesidagencies
     *
     * @return FullSix\ProjectForecastBundle\Entity\Agencies
     */
    public function getAgenciesidagencies() {
        return $this->agenciesidagencies;
    }

    /**
     * Set customerscreatedby
     *
     * @param FullSix\ProjectForecastBundle\Entity\Users $customerscreatedby
     * @return Customers
     */
    public function setCustomerscreatedby(\FullSix\ProjectForecastBundle\Entity\Users $customerscreatedby = null) {
        $this->customerscreatedby = $customerscreatedby;

        return $this;
    }

    /**
     * Get customerscreatedby
     *
     * @return FullSix\ProjectForecastBundle\Entity\Users
     */
    public function getCustomerscreatedby() {
        return $this->customerscreatedby;
    }

    public function __toString() {
        return (string) $this->getCustomersname();
    }

    /**
     * Get sapids
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getSapids()
    {
        if (!is_array($this->sapids)) {
            return $this->sapids->toArray();
        } else {
            return $this->sapids;
        }
    }

    public function setSapids($sapids)
    {
        $this->sapids = $sapids;

        return $this;
    }


    public function getMergedWith()
    {
        return $this->mergedWith;
    }

    public function setMergedWith($mergedWith)
    {
        $this->mergedWith = $mergedWith;

        return $this;
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
     * Get resourceprofiles
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function setResourceprofiles($resourceprofiles)
    {
        $this->resourceprofiles = $resourceprofiles;
    }

    public function getProjects()
    {
        if (!is_array($this->projects)) {
            return $this->projects->toArray();
        } else {
            return $this->projects;
        }
    }
    /**
     * Get projects
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function setProjects($projects)
    {
        $this->projects = $projects;
    }

    public function checkActivation()
    {
        $err = array();

        if($this->getIsTrashed() == true){
            //test for activation
            if($this->mergedWith != null){
                array_push($err, "client.cannotActivate.alreadyMearged");
            }
            if ($this->getAgenciesidagencies()) {
                if ($this->getAgenciesidagencies()->getIsTrashed() == true) {
                    array_push($err, "client.cannotActivate.inactiveAgency");
                }
            }

        }else{
            //test for deactivation
        }

        return $err;
    }

}
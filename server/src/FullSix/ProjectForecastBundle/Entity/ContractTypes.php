<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use FullSix\ProjectForecastBundle\Entity\NonDeletableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * ContractTypes
 *
 * @ORM\Table(name="ContractTypes")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 *
 * @UniqueEntity(fields="contracttypesname",
 * errorPath="",
 * message=  "Il existe déjà un autre type de contrat avec le même nom.")
 */
class ContractTypes extends NonDeletableEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     *  @GRID\Column(visible=false, filterable=false)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="ContractTypesName", type="string", length=50, nullable=false)
     *
     * @GRID\Column(title="common.field.label")
     */
    private $contracttypesname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ContractTypesUpdated", type="datetime", nullable=true)
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $contracttypesupdated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="Contracttypescreated", type="datetime", nullable=false)
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $contracttypescreated;


    /**
     * @ORM\OneToMany(targetEntity="Contracts", mappedBy="contracttype")
     *
     */
    protected $contracts;

    /**
     * @var boolean $contractTypesIsExternal
     *
     */
    private $contractTypesIsExternal;


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
     * Set contracttypesname
     *
     * @param string $contracttypesname
     *
     * @return ContractTypes
     */
    public function setContracttypesname($contracttypesname)
    {
        $this->contracttypesname = $contracttypesname;

        return $this;
    }

    /**
     * Get contracttypesname
     *
     * @return string
     */
    public function getContracttypesname()
    {
        return $this->contracttypesname;
    }

    /**
     * Set contracttypesupdated
     *
     * @ORM\PreUpdate
     *
     * @return ContractTypes
     */
    public function setContracttypesupdated($contracttypesupdated)
    {
        $this->contracttypesupdated = new \DateTime();

        return $this;
    }

    /**
     * Get contracttypesupdated
     *
     * @return \DateTime
     */
    public function getContracttypesupdated()
    {
        return $this->contracttypesupdated;
    }

    /**
     * Set contracttypescreated
     *
     * @ORM\PrePersist
     *
     * @return ContractTypes
     */
    public function setContracttypescreated($contracttypescreated)
    {
        $this->contracttypescreated = new \DateTime();

        return $this;
    }

    /**
     * Get contracttypescreated
     *
     * @return \DateTime
     */
    public function getContracttypescreated()
    {
        return $this->contracttypescreated;
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

    public function checkActivation()
    {
        $err = array();
        $now = new \DateTime();

        if ($this->getIsTrashed() == true){
//            //test for activation

        } else {
            //test for deactivation
            foreach ($this->getContracts() as $contract) {
                //check if no active future and curent contracts
            }
        }

        return $err;
    }

    public function getContractTypesIsExternal() {
        return $this->contractTypesIsExternal;
    }


    public function setContractTypesIsExternal($contractTypesIsExternal) {
        $this->contractTypesIsExternal = $contractTypesIsExternal;

        return $this;
    }

}


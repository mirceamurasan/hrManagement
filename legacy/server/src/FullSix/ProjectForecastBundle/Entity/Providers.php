<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use APY\DataGridBundle\Grid\Mapping as GRID;
use FullSix\ProjectForecastBundle\Entity\NonDeletableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * FullSix\ProjectForecastBundle\Entity\Providers
 *
 * @ORM\Table(name="Providers")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 *
 * @UniqueEntity(fields="providersname",
 *     errorPath="",
 *     message=" Il existe déjà un autre prestataire avec le même nom.")
 */
class Providers extends NonDeletableEntity{

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
     * @var string $providersname
     *
     * @ORM\Column(name="ProvidersName", type="string", length=50, nullable=false)
     *
     * @GRID\Column(title="common.field.name")
     */
    private $providersname;

    /**
     * @var \DateTime $providerscreated
     *
     * @ORM\Column(name="ProvidersCreated", type="datetime", nullable=false)
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $providerscreated;

    /**
     * @var string $providersupdated
     *
     * @ORM\Column(name="ProvidersUpdated", type="datetime", nullable=true)
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $providersupdated;

    /**
     * @ORM\OneToMany(targetEntity="Workers", mappedBy="idprovider")
     */
    private $workers;

    public function __construct() {
        $this->workers = new ArrayCollection();
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
     * Set providersname
     *
     * @param string $providersname
     * @return Providers
     */
    public function setProvidersname($providersname) {
        $this->providersname = $providersname;

        return $this;
    }

    /**
     * Get providersname
     *
     * @return string
     */
    public function getProvidersname() {
        return $this->providersname;
    }


    /**
     * Set providerscreated
     *
     * @ORM\PrePersist
     * @return Providers
     */
    public function setProviderscreated() {
        $this->providerscreated = new \DateTime();

        return $this;
    }

    /**
     * Get providerscreated
     *
     * @return \DateTime
     */
    public function getProviderscreated() {
        return $this->providerscreated;
    }

    /**
     * Set providersupdated
     *
     * @ORM\PreUpdate
     * @return Providers
     */
    public function setProvidersupdated() {
        $this->providersupdated = new \DateTime();

        return $this;
    }

    /**
     * Get providersupdated
     *
     * @return string
     */
    public function getProvidersupdated() {
        return $this->providersupdated;
    }

	/**
     * Add workers
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Workers $workers
     * @return Providers
     */
    public function addWorkers(\FullSix\ProjectForecastBundle\Entity\Workers $workers)
    {
        $this->workers[] = $workers;

        return $this;
    }

    /**
     * Remove workers
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Workers $workers
     */
    public function removeWorkers(\FullSix\ProjectForecastBundle\Entity\Workers $workers)
    {
        $this->workers->removeElement($workers);
    }

    /**
     * get workers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getWorkers()
    {
		return $this->workers;
	}

    public function __toString() {
        return (string) $this->getProvidersname();
    }

}

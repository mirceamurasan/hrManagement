<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use FullSix\ProjectForecastBundle\Entity\NonDeletableEntity;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Locations
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields="locationsname",
 *     errorPath="",
 *     message="Il existe déjà un autre emplacement avec le même nom")
 */
class Locations extends NonDeletableEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="LocationsName", type="string", length=25)
     *
     * @GRID\Column(title="common.field.label")
     */
    private $locationsname;

    /**
     * @var string
     *
     * @ORM\Column(name="LocationsAddress", type="string", length=255, nullable=true)
     *
     * @GRID\Column(title="admin.location.address")
     */
    private $locationsaddress;


    /**
     * @var \DateTime $locationscreated
     *
     * @ORM\Column(name="LocationsCreated", type="datetime", nullable=false)
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $locationscreated;

    /**
     * @var \DateTime $locationsupdated
     *
     * @ORM\Column(name="LocationsUpdated", type="datetime", nullable=true)
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $locationsupdated;

    /**
     *
     * @ORM\OneToMany(targetEntity="Teams", mappedBy="teamslocation")
     */
    private $teams;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
    }

    /**
     * Add teams
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Teams $teams
     * @return Locations
     */
    public function addTeam(\FullSix\ProjectForecastBundle\Entity\Teams $teams)
    {
        $this->teams[] = $teams;

        return $this;
    }

    /**
     * Remove teams
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Teams $teams
     */
    public function removeTeam(\FullSix\ProjectForecastBundle\Entity\Teams $teams)
    {
        $this->teams->removeElement($teams);
    }

    /**
     * get teams
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTeams()
    {
        if (!is_array($this->teams)) {
            return $this->teams->toArray();
        } else {
            return $this->teams;
        }
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
     * Set LocationsName
     *
     * @param string $LocationsName
     *
     * @return Locations
     */
    public function setLocationsname($locationsname)
    {
        $this->locationsname = $locationsname;

        return $this;
    }

    /**
     * Get LocationsName
     *
     * @return string
     */
    public function getLocationsname()
    {
        return $this->locationsname;
    }

    /**
     * Set LocationsAddress
     *
     * @param string $LocationsAddress
     *
     * @return Locations
     */
    public function setLocationsaddress($locationsaddress)
    {
        $this->locationsaddress = $locationsaddress;

        return $this;
    }

    /**
     * Get LocationsAddress
     *
     * @return string
     */
    public function getLocationsaddress()
    {
        return $this->locationsaddress;
    }

    /**
     * Get locationscreated
     *
     * @return \DateTime
     */
    public function getLocationscreated()
    {
        return $this->locationscreated;
    }

    /**
     * Get locationsupdated
     *
     * @return \DateTime
     */
    public function getLocationsupdated()
    {
        return $this->locationsupdated;
    }

    /**
     * Set locationscreated
     *
     * @ORM\PrePersist
     * @return Locations
     */
    public function setLocationscreated()
    {
        $this->locationscreated = new \DateTime();

        return $this;
    }

    /**
     * Set locationsupdated
     *
     * @ORM\PreUpdate
     * @return Locations
     */
    public function setLocationsupdated()
    {
        $this->locationsupdated = new \DateTime();

        return $this;
    }

        public function checkActivation()
    {
        $err = array();

        if ($this->getIsTrashed() == true){
            //test for activation

        } else {
            //test for deactivation
            foreach ($this->getTeams() as $team) {
                if ($team->getIsTrashed() == false) {
                    array_push($err, "admin.location.error.cannotDeactivate.activeTeams");
                    break;
                }
            }

        }

        return $err;
    }

}


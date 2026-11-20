<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use FullSix\ProjectForecastBundle\Entity\NonDeletableEntity;
use APY\DataGridBundle\Grid\Mapping as GRID;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * FullSix\ProjectForecastBundle\Entity\Team
 *
 * @ORM\Table(name="Teams")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 *
 * @UniqueEntity(fields="teamsname",
 *     errorPath="",
 *     message=" Il existe déjà un autre équipe avec le même nom.")
 *
 * @GRID\Source(columns="teamsname, teamslocation.locationsname, isTrashed, id")
 */
class Teams extends NonDeletableEntity
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @GRID\Column(visible=false, filterable=false)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="TeamName", type="string", length=50, nullable=false, unique=true)
     *
     * @GRID\Column(title="common.field.name")
     */
    protected $teamsname;

    /**
     * @ORM\ManyToOne(targetEntity="Locations", inversedBy="teams")
     * @ORM\JoinColumn(name="TeamsLocation", referencedColumnName="id")
     *
     * @GRID\Column(field="teamslocation.locationsname", title="admin.location.label")
     */
    protected $teamslocation;

    /**
     *
     * @ORM\OneToMany(targetEntity="Workers", mappedBy="team")
     */
    protected $teamsworkers;

    public function __construct()
    {
        $this->teamsworkers = new ArrayCollection();
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
     * Set teamsname
     *
     * @param string $teamsname
     *
     * @return Teams
     */
    public function setTeamsname($teamsname)
    {
        $this->teamsname = $teamsname;

        return $this;
    }

    /**
     * Get teamsname
     *
     * @return string
     */
    public function getTeamsname()
    {
        return $this->teamsname;
    }

    /**
     * Get teamslocation
     *
     * @return \FullSix\ProjectForecastBundle\Entity\Locations
     */
    public function getTeamslocation()
    {
        return $this->teamslocation;
    }

    /**
     * Set teamslocation
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Locations $teamslocation
     * @return Workers
     */
    public function setTeamsLocation(\FullSix\ProjectForecastBundle\Entity\Locations $teamslocation = null)
    {
        $this->teamslocation = $teamslocation;

        return $this;
    }

    /**
     * Add teamsworkers
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Workers $teamsworkers
     * @return Teams
     */
    public function addWorker(\FullSix\ProjectForecastBundle\Entity\Workers $teamsworkers)
    {
        $this->teamsworkers[] = $teamsworkers;

        return $this;
    }

    /**
     * Remove teamsworkers
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Workers $wteamsorkers
     */
    public function removeWorker(\FullSix\ProjectForecastBundle\Entity\Workers $workers)
    {
        $this->teamsworkers->removeElement($teamsworkers);
    }

    /**
     * get teamsworkers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTeamsworkers()
    {
        return $this->teamsworkers->toArray();
    }

    public function checkActivation() {
        $err = array();

        if ($this->getIsTrashed() == true) {
            //test for activation
            if ($this->getTeamslocation()) {
                if ($this->getTeamslocation()->getIsTrashed() == true) {
                    array_push($err, "team.cannotActivate.inactiveLocation");
                }
            }
        } else {

        }
        return $err;
    }
}
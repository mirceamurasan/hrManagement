<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Daysoffs
 *
 * //unsued in the current version
 * //@ORM\Table()
 * //@ORM\Entity(repositoryClass="FullSix\ProjectForecastBundle\Repository\DaysoffsRepository")
 */
class Daysoffs
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Users
     *
     * @ORM\ManyToOne(targetEntity="Users", inversedBy="id")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="DaysoffsUsersId", referencedColumnName="id", nullable=false)
     * })
     */
    protected $daysoffsuserid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DaysoffsDate", type="date", nullable=false)
     */
    private $DaysoffsDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="DaysoffsMorning", type="boolean", nullable=false)
     */
    private $DaysoffsMorning;

    /**
     * @var boolean
     *
     * @ORM\Column(name="DaysoffsAfternoon", type="boolean", nullable=false)
     */
    private $DaysoffsAfternoon;


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
     * Set DaysoffsDate
     *
     * @param \DateTime $daysoffsDate
     * @return Daysoffs
     */
    public function setDaysoffsDate($daysoffsDate)
    {
        $this->DaysoffsDate = $daysoffsDate;
    
        return $this;
    }

    /**
     * Get DaysoffsDate
     *
     * @return \DateTime 
     */
    public function getDaysoffsDate()
    {
        return $this->DaysoffsDate;
    }

    /**
     * Set DaysoffsMorning
     *
     * @param boolean $daysoffsMorning
     * @return Daysoffs
     */
    public function setDaysoffsMorning($daysoffsMorning)
    {
        $this->DaysoffsMorning = $daysoffsMorning;
    
        return $this;
    }

    /**
     * Get DaysoffsMorning
     *
     * @return boolean 
     */
    public function getDaysoffsMorning()
    {
        return $this->DaysoffsMorning;
    }

    /**
     * Set DaysoffsAfternoon
     *
     * @param boolean $daysoffsAfternoon
     * @return Daysoffs
     */
    public function setDaysoffsAfternoon($daysoffsAfternoon)
    {
        $this->DaysoffsAfternoon = $daysoffsAfternoon;
    
        return $this;
    }

    /**
     * Get DaysoffsAfternoon
     *
     * @return boolean 
     */
    public function getDaysoffsAfternoon()
    {
        return $this->DaysoffsAfternoon;
    }

    /**
     * Set daysoffsuserid
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Users $daysoffsuserid
     * @return Daysoffs
     */
    public function setDaysoffsuserid(\FullSix\ProjectForecastBundle\Entity\Users $daysoffsuserid)
    {
        $this->daysoffsuserid = $daysoffsuserid;
    
        return $this;
    }

    /**
     * Get daysoffsuserid
     *
     * @return \FullSix\ProjectForecastBundle\Entity\Users 
     */
    public function getDaysoffsuserid()
    {
        return $this->daysoffsuserid;
    }

    /**
     * Get User
     *
     * @return \FullSix\ProjectForecastBundle\Entity\Users
     */
    public function getUser()
    {
        return $this->daysoffsuserid;
    }
}
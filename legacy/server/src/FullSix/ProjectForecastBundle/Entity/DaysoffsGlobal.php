<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * DaysoffsGlobal
 *
 * @ORM\Table(name="DaysoffsGlobal", indexes={@ORM\Index(name="date_idx", columns={"DaysoffsGlobalDate"})})
 * @ORM\Entity(repositoryClass="FullSix\ProjectForecastBundle\Repository\DaysoffsGlobalRepository")
 * @UniqueEntity(fields="DaysoffsGlobalDate",
 *     errorPath="",
 *     message="Il existe déjà un autre jour férié pour la même date")
 * @UniqueEntity(fields="DaysoffsGlobalName",
 *     errorPath="",
 *     message=" Il existe déjà un autre jour férié avec le même nom")
 *  */
class DaysoffsGlobal
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
     * @var \DateTime
     *
     * @ORM\Column(name="DaysoffsGlobalDate", type="date", unique=true)
     *
     * @GRID\Column(title="Date", inputType="date")
     *
     */
    private $DaysoffsGlobalDate;

    /**
     * @var string
     *
     * @ORM\Column(name="DaysoffsGlobalName", type="string", length=100, unique=true)
     *
     * @GRID\Column(title="Label")
     */
    private $DaysoffsGlobalName;


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
     * Set DaysoffsGlobalDate
     *
     * @param \DateTime $daysoffsGlobalDate
     * @return DaysoffsGlobal
     */
    public function setDaysoffsGlobalDate($daysoffsGlobalDate)
    {
        $this->DaysoffsGlobalDate = $daysoffsGlobalDate;

        return $this;
    }

    /**
     * Get DaysoffsGlobalDate
     *
     * @return \DateTime
     */
    public function getDaysoffsGlobalDate()
    {
        return $this->DaysoffsGlobalDate;
    }

    /**
     * Set DaysoffsGlobalName
     *
     * @param string $daysoffsGlobalName
     * @return DaysoffsGlobal
     */
    public function setDaysoffsGlobalName($daysoffsGlobalName)
    {
        $this->DaysoffsGlobalName = $daysoffsGlobalName;

        return $this;
    }

    /**
     * Get DaysoffsGlobalName
     *
     * @return string
     */
    public function getDaysoffsGlobalName()
    {
        return $this->DaysoffsGlobalName;
    }
}

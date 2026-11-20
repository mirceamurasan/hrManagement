<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * NonDeletableEntity
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
class NonDeletableEntity
{

    /**
     * @var boolean
     *
     * @ORM\Column(name="isTrashed", type="boolean")
     *
     * @GRID\Column(title="common.field.trashed")
     *
     */
    protected $isTrashed;

    /**
     * Set isTrashed
     *
     * @param boolean $isTrashed
     *
     * @return NonDeletableEntity
     */
    public function setIsTrashed($isTrashed)
    {
        $this->isTrashed = $isTrashed;

        return $this;
    }

    /**
     * Get isTrashed
     *
     * @return boolean
     */

    public function getIsTrashed()
    {
        return $this->isTrashed;
    }

   /**
    * @ORM\PrePersist
    *
    */
    public function setIsTrashedFalse()
    {
        $this->isTrashed = false;
    }

    /**
     * check if the entity can be (de)activated
    */
    public function checkActivation(){
        return array();
    }

    /*
     * (de)activate and compute side effects
     * */
    public function performActivationAndSideEffects(){
        //(de)activate
        $this->setIsTrashed(!$this->getIsTrashed());
        //side effects
    }


}


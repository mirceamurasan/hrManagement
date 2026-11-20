<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Gedmo\Loggable\Entity\MappedSuperclass\AbstractLogEntry;

/**
 * FullSix\ProjectForecastBundle\Entity\PfLogEntry
 *
 * @ORM\Table(
 *     name="PfLogEntries",
 *  indexes={
 *      @index(name="pflog_class_lookup_idx", columns={"object_class"}),
 *      @index(name="pflog_date_lookup_idx", columns={"logged_at"}),
 *      @index(name="pflog_user_lookup_idx", columns={"username"}),
 *      @index(name="pflog_version_lookup_idx", columns={"object_id", "object_class", "version"}),
 *  }
 * )
 * @ORM\Entity(repositoryClass="Gedmo\Loggable\Entity\Repository\LogEntryRepository")
 */
class PfLogEntries extends AbstractLogEntry
{
    /**
     * @var text $oldData
     *
     * @ORM\Column(type="array", name="old_data", nullable=true)
     */
    protected $oldData;

    /**
     * Set oldData
     *
     * @param array $oldData
     * @return PfLogEntries
     */
    public function setOldData($oldData)
    {
        $this->oldData = $oldData;

        return $this;
    }

    /**
     * Get oldData
     *
     * @return array
     */
    public function getOldData()
    {
        return $this->oldData;
    }
}
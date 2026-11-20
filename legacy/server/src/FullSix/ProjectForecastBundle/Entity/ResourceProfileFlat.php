<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResourceProfileFlat
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="FullSix\ProjectForecastBundle\Repository\ResourceProfileFlatRepository")
 */
class ResourceProfileFlat
{
    /**
     * @var Customers
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Projects", inversedBy="resourceprofilesflat")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="Id")
     */
    private $projectid;

    /**
     * @var Workers
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Workers", inversedBy="resourceprofilesflat")
     * @ORM\JoinColumn(name="worker_id", referencedColumnName="id")
     */
    private $workerid;

    /**
     * @var string
     *
     * @ORM\Column(name="roles", type="array", nullable=true)
     */
    private $roles;

    public function __construct()
    {
        $this->roles = array();
    }

    /**
     * Set projectid
     *
     * @param string $projectid
     *
     * @return ResourceProfileFlat
     */
    public function setProjectid($projectid)
    {
        $this->projectid = $projectid;

        return $this;
    }

    /**
     * Get projectid
     *
     * @return string
     */
    public function getProjectid()
    {
        return $this->projectid;
    }

    /**
     * Set workerid
     *
     * @param string $workerid
     *
     * @return ResourceProfileFlat
     */
    public function setWorkerid($workerid)
    {
        $this->workerid = $workerid;

        return $this;
    }

    /**
     * Get workerid
     *
     * @return string
     */
    public function getWorkerid()
    {
        return $this->workerid;
    }

    /**
     * Set roles
     *
     * @param string $roles
     *
     * @return ResourceProfileFlat
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return string
     */
    public function getRoles()
    {
        return $this->roles;
    }
}


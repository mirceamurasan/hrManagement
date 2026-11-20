<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResourceProfile
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="FullSix\ProjectForecastBundle\Repository\ResourceProfileRepository")
 */
class ResourceProfile
{
    /**
     * @var Customers
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Customers", inversedBy="resourceprofiles")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="Id")
     */
    private $customerid;

    /**
     * @var Workers
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Workers", inversedBy="resourceprofiles")
     * @ORM\JoinColumn(name="worker_id", referencedColumnName="id")
     */
    private $workerid;



    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="array", nullable=true)
     */
    private $roles;

    public function __construct()
    {
        $this->roles = array();
    }

    /**
     * Set customerid
     *
     * @param string $customerid
     *
     * @return ResourceProfile
     */
    public function setCustomerid($customerid)
    {
        $this->customerid = $customerid;

        return $this;
    }

    /**
     * Get customerid
     *
     * @return string
     */
    public function getCustomerid()
    {
        return $this->customerid;
    }

    /**
     * Set workerid
     *
     * @param string $workerid
     *
     * @return ResourceProfile
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
     * @return ResourceProfile
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


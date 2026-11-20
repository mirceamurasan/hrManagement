<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use FullSix\ProjectForecastBundle\Entity\Customers;

/**
 * CustomersSapIds
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class CustomersSapIds
{
    /**
     * @var integer
     *
     * @ORM\Column(name="Id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $sapid
     *
     * @ORM\Column(name="SapId", type="string", length=50)
     */
    protected $sapid;

    /**
     * @var Customers
     *
     * @ORM\ManyToOne(targetEntity="Customers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="CustomersIdCustomers", referencedColumnName="Id")
     * })
     */
    private $customers;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function getSapid() {
        return $this->sapid;
    }

    public function getCustomers() {
        return $this->customers;
    }

    public function setSapid($sapid) {
        $this->sapid = $sapid;
    }

    public function setCustomers(Customers $customers) {
        $this->customers = $customers;
    }

}


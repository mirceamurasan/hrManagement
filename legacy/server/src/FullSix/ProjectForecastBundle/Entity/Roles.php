<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * Roles
 *
 * @ORM\Table(name="Roles")
 * @ORM\Entity(repositoryClass="FullSix\ProjectForecastBundle\Repository\RolesRepository")
 *
 * @GRID\Source(columns="id, name, value, roleForUser")
 */
class Roles
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @GRID\Column(visible=false)
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     *
     * @GRID\Column(title="Role name")
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=50)
     *
     * @GRID\Column(title="Value")
     */
    protected $value;

    /**
     * @var boolean
     *
     */
    private $roleForUser;

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
     * Set name
     *
     * @param string $name
     * @return Roles
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function __toString() {
        return $this->getName();
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setRoleForUser($roleForUser)
    {
        $this->roleForUser = $roleForUser;
        return $this;
    }

    public function getRoleForUser()
    {
        return $this->roleForUser;
    }
}
<?php

namespace FullSix\ProjectForecastBundle\Entity;

use FOS\UserBundle\Entity\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use APY\DataGridBundle\Grid\Mapping as GRID;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * FullSix\ProjectForecastBundle\Entity\Groups
 *
 * @ORM\Table(name="Groups")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 *
 * @GRID\Source(columns="id, name, groupUsers.id:count, isTrashed", groupBy={"id", "name"})
 *
 * @UniqueEntity(fields="name",
 *   errorPath="",
 *   message="Il existe déjà un autre groupe avec le même nom.")
 */
class Groups extends BaseGroup
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
    protected $id;

    /**
     * @GRID\Column(title="Name")
     */
    protected $name;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Users", inversedBy="groups")
     * @ORM\JoinTable(name="UsersHasGroups",
     *   joinColumns={
     *     @ORM\JoinColumn(name="Groupsid", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="Usersid", referencedColumnName="id")
     *   }
     * )
     *
     * @GRID\Column(field="groupUsers.id:count", title="Membres", filterable=false)
     */
    protected $groupUsers;

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
     * Constructor
     */
    public function __construct()
    {
        $this->groupUsers = new ArrayCollection();
        $this->roles = array();
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
     * Add groupUsers
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Users $groupUsers
     * @return Groups
     */
    public function addGroupUser(\FullSix\ProjectForecastBundle\Entity\Users $groupUsers)
    {
        $this->groupUsers[] = $groupUsers;

        return $this;
    }

    /**
     * Remove groupUsers
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Users $groupUsers
     */
    public function removeGroupUser(\FullSix\ProjectForecastBundle\Entity\Users $groupUsers)
    {
        $this->groupUsers->removeElement($groupUsers);
    }

    /**
     * Get groupUsers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroupUsers()
    {
        return $this->groupUsers;
    }

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
        $this->roles = array();
    }
}

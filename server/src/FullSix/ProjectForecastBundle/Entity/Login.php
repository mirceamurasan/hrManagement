<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
//use FOS\UserBundle\Entity\User as BaseUser;
//use IMAG\LdapBundle\User\LdapUserInterface;

/**
 * FullSix\ProjectForecastBundle\Entity\Login
 *
 * @ORM\Table(name="Login", uniqueConstraints={@ORM\UniqueConstraint(name="username_idx", columns={"username_canonical", "dirty"})})
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity(repositoryClass="FullSix\ProjectForecastBundle\Repository\LoginRepository")
 * @ORM\AttributeOverrides({
 *      @ORM\AttributeOverride(name="usernameCanonical",
 *          column=@ORM\Column(
 *              name="username_canonical",
 *              type="string",
 *              length=255)
 *          ),
 *      @ORM\AttributeOverride(name="emailCanonical",
 *          column=@ORM\Column(
 *              name="email_canonical",
 *              type="string",
 *              length=255)
 *          )
 *  })
 */

class Login extends \FOS\UserBundle\Entity\User
{
    // THE EMAIL FIELD IS ONLY USED BECAUSE OF THE INHERITANCE (THIS FIELD IS NEVER EXPOSED). ALL EMAIL RELATED FUCNTIONALITY IS DONE IN THE USERS ENTITY.

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var boolean $dirty
     *
     * @ORM\Column(name="dirty", type="boolean", nullable=false)
     */
    protected $dirty;

    /**
     * @var Integer $user
     *
     * @ORM\OneToOne(targetEntity="Users", mappedBy="login")
     */
    protected $user;


    public function __construct()
    {
        parent::__construct();

        $this->dirty = false;
        $this->roles = array();
        $this->expiresAt = null;
    }

    /**
     * Get expiresAt
     *
     * @return \Datetime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Returns the user roles
     *
     * @return array The roles
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Get dirty
     *
     * @return boolean
     */
    public function getDirty()
    {
        return $this->dirty;
    }

    /**
     * Get user
     *
     * @return \FullSix\ProjectForecastBundle\Entity\Users
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get dn
     */
    public function getDn()
    {
        return $this->user->getDn();
    }

    /**
     * Get cn
     */
    public function getCn()
    {
        return $this->user->getCn();
    }

    /**
     * Get attributes
     */
    public function getAttributes()
    {
        return $this->user->getAttributes();
    }

    /**
     * Get specific attribute from attributes
     *
     * @param string $name
     * @return string
     */
    public function getAttribute($name)
    {
        return $this->user->getAttribute($name);
    }



    /**
     * @return boolean
     */
    public function isDirty() {
        return $this->getDirty();
    }

    /**
     * @return boolean
     */
    public function isNotDirty() {
        return !$this->getDirty();
    }



    /**
     * Set dirty
     *
     * @param boolean $dirty
     *
     * @return Groups
     */
    public function setDirty($dirty)
    {
        $this->dirty = $dirty;

        return $this;
    }

    /**
     * Set user
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Users $user
     * @return Login
     */
    public function setUser(\FullSix\ProjectForecastBundle\Entity\Users $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Set dn
     *
     * @param string $userldapdn
     * @return Login
     */
    public function setDn($userldapdn)
    {
        $this->user->setDn($userldapdn);

        return $this;
    }

    /**
     * Set dn
     *
     * @param string $dn
     * @return Login
     */
    public function setCn($userldapcn)
    {
        $this->user->setCn($userldapcn);

        return $this;
    }

    /**
     * Set attributes
     *
     * @param string $attributes
     * @return Login
     */
    public function setAttributes(array $attributes)
    {
        $this->user->setAttributes($attributes);

        return $this;
    }

    /**
     * Finds if the LDAP user is the same as the local current user
     *
     * @param \Symfony\Component\Security\Core\User\UserInterface $name
     * @return bool
     */
    public function isEqualTo(UserInterface $login)
    {
        if ($login->getUsername() !== $this->username
            || $login->getEmail() !== $this->email
//            || !$this->user->isEqualTo($login->user)
            || count(array_diff($login->getRoles(), $this->getRoles())) > 0
        ) {
            return false;
        }

        return true;
    }

    /**
     * Serializes the user.
     *
     * The serialized data have to contain the fields used by the equals method and the username.
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled,
            $this->id,
        ));
    }

    /**
     * Unserializes the user.
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        // add a few extra elements in the array to ensure that we have enough keys when unserializing
        // older data which does not include all properties.
        $data = array_merge($data, array_fill(0, 2, null));

        list(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled,
            $this->id
        ) = $data;
    }

    public function __toString()
    {
        return (string) $this->getUsername();
    }

    public function setExpiresAtNull()
    {
        $this->expiresAt = null;

        return $this;
    }

    public function setExpiresAt(\DateTime $date = null)
    {
        $this->expiresAt = $date;
        return $this;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function computeServiceProvider()
    {
        $date = $this->getExpiresAt();
        if(!$date || $date >=  new \DateTime("today")){
            $this->setEnabled(true);
        }else{
            $this->setEnabled(false);
        }
    }
}

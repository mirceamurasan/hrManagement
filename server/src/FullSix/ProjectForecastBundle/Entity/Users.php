<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * FullSix\ProjectForecastBundle\Entity\Users
 *
 * @ORM\Table(name="Users")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="FullSix\ProjectForecastBundle\Repository\UsersRepository")
 * @GRID\Source(columns="id, userslastname, usersfirstname, login.username, login.roles, login.expiresAt, mergedWith.login.username, adLinked, worker.id")
 */
class Users
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
     * @var string $email
     *
     * The email is also stored in the Login entity. The Login field is never exposed.
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     * @GRID\Column(title="E-mail")
     */
    protected $email;

    /**
    * @var string $usersfirstname
    *
    * @ORM\Column(name="UsersFirstName", type="string", length=150, nullable=true)
    *
    * @GRID\Column(title="Firstname")
    **/
    protected $usersfirstname;

    /**
    * @var string $userslastname
    * @ORM\Column(name="UsersLastName", type="string", length=150, nullable=true)
    * @GRID\Column(title="Nom")
    **/
    protected $userslastname;

    /**
    * @var string $usersinitials
    * @ORM\Column(name="UsersInitials", type="string", length=5, nullable=true)
    * @GRID\Column(title="Initiales")
    **/
    protected $usersinitials;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Groups", mappedBy="groupUsers")
     */
    protected $groups;

    /**
    * @var string $phoneNumber
    * @ORM\Column(name="phone_number", type="string", length=255, nullable=true)
    * @GRID\Column(title="Phone number")
    **/
    protected $phoneNumber;

    /**
    * @var date $birthDate
    * @ORM\Column(name="birthdate", type="date", nullable=true)
    * @GRID\Column(title="Birthday")
    **/
    protected $birthDate;

    /**
    * @var string $contactMsn
    * @ORM\Column(name="contact_msn", type="string", length=255, nullable=true)
    * @GRID\Column(title="Contact MSN")
    **/
    protected $contactMsn;

    /**
    * @var string $contactFacebook
    * @ORM\Column(name="contact_facebook", type="string", length=255, nullable=true)
    * @GRID\Column(title="Contact Facebook")
    **/
    protected $contactFacebook;

    /**
    * @var string $contactTwitter
    * @ORM\Column(name="contact_twitter", type="string", length=255, nullable=true)
    * @GRID\Column(title="Contact Twitter")
    **/
    protected $contactTwitter;

    /**
    * @var string $message
    * @ORM\Column(name="message", type="string", nullable=true)
    * @GRID\Column(title="Message")
    **/
    protected $message;

    /**
     * @var $login
     *
     * @ORM\OneToOne(targetEntity="Login", inversedBy="user", cascade={"persist"})
     * @ORM\JoinColumn(name="login_id", referencedColumnName="id")
     * @GRID\Column(field="login.username", title="Login")
     * @GRID\Column(field="login.roles", title="Role")
     * @GRID\Column(field="login.expiresAt", title="common.field.activeM", filterable=false)
     */
    protected $login;

    /**
     * @var FullSix\ProjectForecastBundle\Entity\Workers $worker
     *
     * @ORM\OneToOne(targetEntity="Workers", mappedBy="user")
     * @GRID\Column(field="worker.id", title="worker.label", filterable=true, operators={"like", "isNull", "isNotNull"})
     */
    protected $worker;

    protected $adLinked;

    /**
     * @ORM\Column(name="UserLdapDn", type="string", length=255)
     */
    protected $userldapdn;

    protected $userldapcn;


    protected $attributes;

    /**
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumn(name="mergedWith", referencedColumnName="id", nullable=true)
     * @GRID\Column(field="mergedWith.login.username", title="common.field.mergedWith", filterable=true, operators={"like", "isNull", "isNotNull"})
     */
    protected $mergedWith;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->adLinked = false;
        $this->userldapdn = '';
    }

    public function __toString()
    {
        return $this->getCheckedName();
    }

    /**
     * Return better between username and CompleteName
     *
     * @return string
     */
    public function getCheckedName()
    {
        if(strlen($this->getCompleteName()) > 1) {
            return $this->getCompleteName();
        } else {
            return $this->email;
        }
    }

    /**
     * @return string
     *
     * @GRID\Column(title="User")
     */
    public function getCompleteName()
    {
        //return strtoupper($this->userslastname). ' ' . ucfirst($this->usersfirstname);
        return $this->userslastname. ' ' . $this->usersfirstname;
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


    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set usersfirstname
     *
     * @param string $usersfirstname
     * @return Users
     */
    public function setUsersfirstname($input)
    {
        $this->usersfirstname = $input;

        return $this;
    }

    /**
     * Get usersfirstname
     *
     * @return string
     */
    public function getUsersfirstname()
    {
        return $this->usersfirstname;
    }

    /**
     * Set userslastname
     *
     * @param string $userslastname
     * @return Users
     */
    public function setUserslastname($input)
    {
        $this->userslastname = $input;

        return $this;
    }

    /**
     * Get userslastname
     *
     * @return string
     */
    public function getUserslastname() {
        return $this->userslastname;
    }

    /**
     * Set usersinitials
     *
     * @param string $usersinitials
     * @return Users
     */
    public function setUsersinitials($input)
    {
        $this->usersinitials = $input;

        return $this;
    }

    /**
     * Get usersinitials
     *
     * @return string
     */
    public function getUsersinitials()
    {
        return $this->usersinitials;
    }

    /**
     * Add group
     *
     * @param FullSix\ProjectForecastBundle\Entity\Groups $group
     * @return Users
     */
    public function addGroup(\FullSix\ProjectForecastBundle\Entity\Groups $group)
    {
        $this->groups[] = $group;
        return $this;
    }

    /**
     * Remove group
     *
     * @param FullSix\ProjectForecastBundle\Entity\Groups $group
     */
    public function removeGroup(\FullSix\ProjectForecastBundle\Entity\Groups $group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * Get groups
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Set login
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Login $login
     * @return Users
     */
    public function setLogin(\FullSix\ProjectForecastBundle\Entity\Login $login = null)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return \FullSix\ProjectForecastBundle\Entity\Login
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set worker
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Workers $worker
     * @return Users
     */
    public function setWorker(\FullSix\ProjectForecastBundle\Entity\Workers $worker = null)
    {
        $this->worker = $worker;

        return $this;
    }

    /**
     * Get worker
     *
     * @return \FullSix\ProjectForecastBundle\Entity\Workers
     */
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     *
     * @return Users
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     *
     * @return Users
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set contactMsn
     *
     * @param string $contactMsn
     *
     * @return Users
     */
    public function setContactMsn($contactMsn)
    {
        $this->contactMsn = $contactMsn;

        return $this;
    }

    /**
     * Get contactMsn
     *
     * @return string
     */
    public function getContactMsn()
    {
        return $this->contactMsn;
    }

    /**
     * Set contactFacebook
     *
     * @param string $contactFacebook
     *
     * @return Users
     */
    public function setContactFacebook($contactFacebook)
    {
        $this->contactFacebook = $contactFacebook;

        return $this;
    }

    /**
     * Get contactFacebook
     *
     * @return string
     */
    public function getContactFacebook()
    {
        return $this->contactFacebook;
    }

    /**
     * Set contactTwitter
     *
     * @param string $contactTwitter
     *
     * @return Users
     */
    public function setContactTwitter($contactTwitter)
    {
        $this->contactTwitter = $contactTwitter;

        return $this;
    }

    /**
     * Get contactTwitter
     *
     * @return string
     */
    public function getContactTwitter()
    {
        return $this->contactTwitter;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Users
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set adLinked
     *
     * @param boolean $adLinked
     *
     * @return Users
     */
    public function setAdLinked($adLinked)
    {
        $this->adLinked = $adLinked;

        return $this;
    }

    /**
     * Get adLinked
     *
     * @return boolean
     */
    public function getAdLinked()
    {
        return $this->adLinked;
    }

    /**
     * Set userldapdn
     *
     * @param string $userldapdn
     *
     * @return Users
     */
    public function setUserldapdn($userldapdn)
    {
        $this->userldapdn = $userldapdn;

        return $this;
    }

    /**
     * Get userldapdn
     *
     * @return string
     */
    public function getUserldapdn()
    {
        return $this->userldapdn;
    }

    /**
     * Get dn
     */
    public function getDn()
    {
        return $this->userldapdn;
    }

    /**
     * Set dn
     *
     * @param string $userldapdn
     * @return Users
     */
    public function setDn($userldapdn)
    {
        $this->userldapdn = $userldapdn;

        return $this;
    }

    /**
     * Get cn
     */
    public function getCn()
    {
        return $this->userldapcn;
    }

    /**
     * Set dn
     *
     * @param string $dn
     * @return Users
     */
    public function setCn($userldapcn)
    {
        $this->userldapcn = $userldapcn;

        return $this;
    }

    /**
     * Get attributes
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Set attributes
     *
     * @param string $attributes
     * @return Users
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * Get specific attribute from attributes
     *
     * @param string $name
     * @return string
     */
    public function getAttribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * Finds if the LDAP user is the same as the local current user
     *
     * @param \FullSix\ProjectForecastBundle\Entity\Users $user
     * @return bool
     */
    public function isEqualTo(Users $user)
    {//TODO
        if (!$user instanceof LdapUserInterface
            || $user->getUsername() !== $this->username
            || $user->getEmail() !== $this->email
            || $user->getDn() !== $this->userldapdn
        ) {
            return false;
        }

        return true;
    }

    public function isAdLinked()
    {
        return $this->adLinked === true;
    }

    public function  getRoles(){
        if($this->login){
            return $this->login->getRoles();
        }else{
            return null;
        }
    }

    public function getMergedWith()
    {
        return $this->mergedWith;
    }

    public function setMergedWith($mergedWith)
    {
        $this->mergedWith = $mergedWith;
    }


    /**
     * Get the serialized version of the Users object
     *
     * @return string
     */
    public function serialize()
    {//TODO
        return serialize(array(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->emailCanonical,
            $this->email,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled,
            $this->id,
            $this->roles,
            $this->userldapdn,
        ));
    }

    /**
     * Populates the Users object from a serialized array
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {//TODO
        list(
            $this->password,
            $this->salt,
            $this->usernameCanonical,
            $this->username,
            $this->emailCanonical,
            $this->email,
            $this->expired,
            $this->locked,
            $this->credentialsExpired,
            $this->enabled,
            $this->id,
            $this->roles,
            $this->userldapdn,
        ) = unserialize($serialized);
    }
}

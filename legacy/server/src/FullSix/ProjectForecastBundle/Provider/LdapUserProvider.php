<?php

namespace FullSix\ProjectForecastBundle\Provider;

use Symfony\Component\Security\Core\Exception\UnsupportedUserException,
    Symfony\Component\Security\Core\Exception\UsernameNotFoundException,
    Symfony\Component\Security\Core\User\UserInterface,
    Symfony\Component\Security\Core\User\UserProviderInterface;

use IMAG\LdapBundle\Manager\LdapManagerUserInterface,
    IMAG\LdapBundle\User\LdapUserInterface,
    FOS\UserBundle\Model\UserManagerInterface,
    FullSix\ProjectForecastBundle\Entity\Users;

/**
 * LDAP User Provider
 *
 * @author Boris Morel
 * @author Juti Noppornpitak <jnopporn@shiroyuki.com>
 */
class LdapUserProvider implements UserProviderInterface
{
    /**
     * @var \IMAG\LdapBundle\Manager\LdapManagerUserInterface
     */
    protected $ldapManager;

    /**
     * @var \FOS\UserBundle\Model\UserManagerInterface
     */
    protected $loginManager;

    /**
     * @var \FullSix\ProjectForecastBundle\Manager\UsersManager
     */
    protected $usersManager;

    /**
     * @var \Symfony\Component\Validator\Validator
     */
    protected $validator;

    /**
     * @var string
     */
    protected $bindUsernameBefore;

    /**
     * Constructor
     *
     * @param LdapManagerUserInterface $ldapManager
     * @param UserManagerInterface     $loginManager
     * @param Validator                $validator
     */
    public function __construct(LdapManagerUserInterface $ldapManager, UserManagerInterface $loginManager, $usersManager, $validator, $bindUsernameBefore = false)
    {
        $this->ldapManager = $ldapManager;
        $this->loginManager = $loginManager;
        $this->usersManager = $usersManager;
        $this->bindUsernameBefore = $bindUsernameBefore;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        // Throw the exception if the username is not provided.
        if (empty($username)) {
            throw new UsernameNotFoundException('The username is not provided.');
        }

        if (true === $this->bindUsernameBefore) {
            $ldapUser = $this->simpleUser($username);
        } else {
            $ldapUser = $this->anonymousSearch($username);
        }

        return $ldapUser;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof LdapUserInterface) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return is_subclass_of($class, '\IMAG\LdapBundle\User\LdapUserInterface');
    }

    private function simpleUser($username)
    {
        $ldapUser = $this->loginManager->createUser();
        $ldapUser->setUsername($username);

        return $ldapUser;
    }

    private function anonymousSearch($username)
    {
        // Throw the exception if the username is not found.
        if(!$this->ldapManager->exists($username)) {
            throw new UsernameNotFoundException(sprintf('User "%s" not found', $username));
        }

        $this->ldapManager
            ->setUsername($username)
            ->doPass();
        $ldapExistingLogin = $this->loginManager->findUserByUsername($username);
        if (!$ldapExistingLogin instanceof UserInterface) {
            $ldapExistingLogin = $this->loginManager->createUser();
            $user = $this->usersManager->createUser();
        } else {
            $user = $ldapExistingLogin->getUser();
        }

        $ldapLogin = $this->populateLogin($ldapExistingLogin);
        $this->loginManager->updateUser($ldapLogin);
        $this->usersManager->updateUser($this->populateUsers($user->setLogin($ldapLogin)));

        return $ldapLogin;
    }

    public function anonymousSearchAllUsers($searchString)
    {
        return $this->ldapManager
                ->setSearchString($searchString)
                ->fetchLdapUsers();
    }

    public function updateUserFromLdapByUsername($username)
    {
        $response = array(
            'missing' => 0,
            'updated' => 0,
        );
        $localLdapLogin = $this->loginManager->findUserByUsername($username);
        if(!$this->ldapManager->exists($username)) {
            if($localLdapLogin->isEnabled()){
                //if it has to be disabled
                $response['missing'] = 1;
                $this->loginManager->updateUser($localLdapLogin->setExpiresAt(new \DateTime("today"))->isEnabled(false));
            }
        } else {
            $response['updated'] = 1;
            $this->usersManager->updateUser($this->populateUsers($localLdapLogin->getUser()));
            $this->loginManager->updateUser($this->populateLogin($localLdapLogin->setEnabled(true)));
        }

        return $response;
    }

    public function createUsersLoginByUsername($username) {
        $response = array(
            'dirty' => 0,
            'created' => 0,
        );
        // Find local login
        $localLdapLogin = $this->loginManager->findUserByUsername($username);
        $createNewLogin = false;
        if (!$localLdapLogin instanceof UserInterface) {
            // Local login not present
            $createNewLogin = true;
        } elseif (!$localLdapLogin->getUser()->isAdLinked()) {
            // Local login present, but not AD linked
            $createNewLogin = true;
            $response['dirty'] = 1; // username present -> dirty
            $this->loginManager->updateUser($localLdapLogin->setDirty(true));
        }

        if ($createNewLogin) {
            $response['created'] = 1; // username present -> dirty
            $newLocalLdapLogin = $this->populateLogin($this->loginManager->createUser());
            $this->loginManager->updateUser($newLocalLdapLogin);

            $newUser = $this->populateUsers($this->usersManager->createUser());
            $this->usersManager->updateUser($newUser->setLogin($newLocalLdapLogin));
        }
        return $response;
    }

    public function populateLogin(UserInterface $login)
    {
        $login
            ->setUsername($this->ldapManager->getUsername())
            ->setEnabled(true)
            ->setPassword('')
            ->setEmail($this->ldapManager->getEmail())
            ->addRole("ROLE_FORECAST_USER")
            ;

        $expiresAt = (int)$this->ldapManager->getAccountExpires() / 10000000 - 11644473600;
        $oneHundredYearsFromNow = new \DateTime("today +100 years");

        if ($expiresAt < $oneHundredYearsFromNow->getTimestamp() && $expiresAt > 0) {
            $login->setExpiresAt(new \DateTime($expiresAt));
        } else {
            $login->setExpiresAtNull();
        }

        return $login;
    }

    public function populateUsers(Users $user)
    {
        $attributes = $this->ldapManager->getAttributes();
        $usersInitials = isset($attributes['initials']) ? $attributes['initials'] : '';
        $user
            ->setEmail($this->ldapManager->getEmail())
            ->setDn($this->ldapManager->getDn())
            ->setCn($this->ldapManager->getCn())
            ->setAdLinked(true)
            ->setAttributes($this->ldapManager->getAttributes())
            ->setUsersfirstname($this->ldapManager->getGivenName())
            ->setUserslastname($this->ldapManager->getSurname())
            ->setUsersinitials($usersInitials)
            ;

        return $user;
    }
}

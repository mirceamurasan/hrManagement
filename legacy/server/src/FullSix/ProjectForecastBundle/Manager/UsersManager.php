<?php

namespace FullSix\ProjectForecastBundle\Manager;

use Doctrine\ORM\EntityManager;
use FullSix\ProjectForecastBundle\Entity\Users;

class UsersManager
{
    protected $entityManager;
    protected $class;
    protected $repository;

    /**
     * Constructor.
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->entityManager = $em;
        $this->repository = $em->getRepository($class);

        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->getName();
    }

    /**
     * {@inheritDoc}
     */
    public function deleteUser(Users $user)
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritDoc}
     */
    public function findUserBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findUsers()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritDoc}
     */
    public function reloadUser(Users $user)
    {
        $this->entityManager->refresh($user);
    }

    /**
     * Updates a user.
     *
     * @param Users $user
     * @param Boolean       $andFlush Whether to flush the changes (default true)
     */
    public function updateUser(Users $user, $andFlush = true)
    {
        $this->entityManager->persist($user);
        if ($andFlush) {
            $this->entityManager->flush();
        }
    }

    /**
     * Returns an empty user instance
     *
     * @return Users
     */
    public function createUser()
    {
        $class = $this->getClass();
        $user = new $class;

        return $user;
    }

    /**
     * Finds a user by email
     *
     * @param string $email
     *
     * @return Users
     */
    public function findUserByEmail($email)
    {
        return $this->findUserBy(array('email' => $this->canonicalizeEmail($email)));
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $class === $this->getClass();
    }
}

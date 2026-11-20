<?php
namespace FullSix\ProjectForecastBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use FullSix\ProjectForecastBundle\Entity\Login;

class LoginTransformer implements DataTransformerInterface
{
    /**
     * Transforms an object to an array.
     *
     * @param  Login|null $issue
     * @return array
     */
    public function transform($login)
    {
        if (null === $login) {
            return "";
        }

        return array();
    }

    /**
     * Transforms an array to an object.
     *
     * @param  array $log
     *
     * @return Login|null
     *
     * @throws TransformationFailedException if object (login) is not found.
     */
    public function reverseTransform($log)
    {
        if (!$log) {
            return null;
        }

        $login = new Login();

        return $login;
    }
}

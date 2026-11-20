<?php
namespace FullSix\ProjectForecastBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class Login extends Constraint
{
    public $message = '';
    public $noPasswordMessage = "login.password";
    public $noUsernameMessage = "login.username";
    public $noRole = "login.roles";

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
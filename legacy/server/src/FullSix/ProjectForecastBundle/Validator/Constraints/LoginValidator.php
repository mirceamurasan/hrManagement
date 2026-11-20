<?php
namespace FullSix\ProjectForecastBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class LoginValidator extends ConstraintValidator
{
    public function validate($login, Constraint $constraint)
    {
        if ($login->getUsername() || $login->getRoles() || $login->getExpiresAt() || ($login->getPlainPassword() && $login->getPassword())) {
            if (!$login->getPlainPassword() && !$login->getPassword() && !$login->getUser()) {
                $this->context->addViolation($constraint->noPasswordMessage);
            }
            if (!$login->getUsername()) {
                $this->context->addViolation($constraint->noUsernameMessage);
            }
            if (!$login->getRoles()) {
                $this->context->addViolation($constraint->noRole);
            }
        }
    }
}
<?php

namespace AppBundle\Validator\Constraint;

use AppBundle\Validator\UniqueEmailAddressValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueEmailAddress
 * @package AppBundle\Validator\Constraint
 * @Annotation
 */
class UniqueEmailAddress extends Constraint
{
    public $message = "A user with e-mail address {{ email }} exists in the system already.";

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return UniqueEmailAddressValidator::class;
    }
}
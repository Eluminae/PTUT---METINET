<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class CheckCampaignDate extends Constraint
{
    public $message = 'La date de fin doit être posterieure à la date de début.';

    public function validatedBy()
    {
        return get_class($this).'Validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}

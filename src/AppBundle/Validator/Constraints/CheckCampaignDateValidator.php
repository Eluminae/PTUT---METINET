<?php

namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CheckCampaignDateValidator extends ConstraintValidator
{
    public function validate($campaign, Constraint $constraint)
    {
        if ($campaign->endDate < $campaign->beginningDate) {
            $this->context->buildViolation($constraint->message)
                ->atPath('endDate')
                ->addViolation();
        }
    }
}

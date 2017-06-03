<?php

namespace AppBundle\Services;

use AppBundle\Models\Administrator;
use AppBundle\Models\Campaign;
use AppBundle\Models\CampaignAdministrator;
use AppBundle\Models\Juror;
use AppBundle\Services\CampaignAuthorizationChecker;

class UserAuthorizationChecker
{
    private $campaignAuthorizationChecker;

    public function __construct(CampaignAuthorizationChecker $campaignAuthorizationChecker)
    {
        $this->campaignAuthorizationChecker = $campaignAuthorizationChecker;
    }

    public function isAllowedToShowCampaign($user, Campaign $campaign)
    {
        if ($user instanceof Administrator) {
            return true;
        }

        if (
            $user instanceof Juror &&
            $this->campaignAuthorizationChecker->isJuror($user, $campaign)
        ) {
            return true;
        }

        if (
            $user instanceof CampaignAdministrator &&
            $user === $campaign->getCreator()
        ) {
            return true;
        }

        return false;
    }

    public function isAllowedToEvaluateCampaign($user, Campaign $campaign)
    {
        if ($user instanceof Administrator) {
            return true;
        }

        if (
            $user instanceof Juror &&
            $this->campaignAuthorizationChecker->isJuror($user, $campaign)
        ) {
            return true;
        }

        return false;
    }
}

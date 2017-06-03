<?php

namespace AppBundle\Services;

use AppBundle\Models\Administrator;
use AppBundle\Models\Campaign;
use AppBundle\Models\CampaignAdministrator;
use AppBundle\Models\Juror;
use AppBundle\Models\Realisation;
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
            $user->getIdentity() === $campaign->getCreator()
        ) {
            return true;
        }

        return false;
    }

    public function isAllowedToGradeCampaign($user, Campaign $campaign)
    {
        if (
            $user instanceof Juror &&
            $this->campaignAuthorizationChecker->isJuror($user, $campaign)
        ) {
            return true;
        }

        return false;
    }

    public function isAllowedToDeleteCampaign($user, Campaign $campaign)
    {
        if ($user instanceof Administrator) {
            return true;
        }

        if (
            $user instanceof CampaignAdministrator &&
            $user->getIdentity() === $campaign->getCreator()
        ) {
            return true;
        }

        return false;
    }
}

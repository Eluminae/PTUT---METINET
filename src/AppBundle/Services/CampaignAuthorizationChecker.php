<?php

namespace AppBundle\Services;

use AppBundle\Models\Campaign;
use AppBundle\Models\Juror;

class CampaignAuthorizationChecker
{
    public function isJuror(Juror $juror, Campaign $campaign)
    {
        $isJuror = false;
        foreach ($campaign->getJurors() as $campaignJuror) {
            if ($juror === $campaignJuror) {
                $isJuror = true;
            }
        }

        return $isJuror;
    }
}

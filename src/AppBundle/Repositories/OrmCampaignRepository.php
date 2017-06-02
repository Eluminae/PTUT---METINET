<?php

namespace AppBundle\Repositories;

use AppBundle\Models\Campaign;
use Doctrine\ORM\EntityRepository;

class OrmCampaignRepository extends EntityRepository
{
    public function findFinished()
    {
        return $this
            ->getEntityManager()
            ->createQuery(
                'SELECT c FROM AppBundle:Campaign c JOIN c.endDate d WHERE d.date < CURRENT_DATE() AND c.status = :status'
            )
            ->setParameter('status', Campaign::RESULTS_PUBLISHED)
            ->getResult()
        ;
    }

    public function findApprovedActive()
    {
        return $this
            ->getEntityManager()
            ->createQuery(
                'SELECT c FROM AppBundle:Campaign c JOIN c.endDate ed JOIN c.beginningDate bd WHERE c.status = :status AND ed.date > CURRENT_DATE() AND bd.date < CURRENT_DATE()'
            )
            ->setParameter('status', Campaign::ACCEPTED)
            ->getResult()
        ;
    }
}

<?php

namespace AppBundle\Repositories;

use Doctrine\ORM\EntityRepository;

class OrmCampaignRepository extends EntityRepository
{
    public function getOneById()
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT campaign FROM Campaign campaign BY campaign.id ASC limit 1'
            )
            ->getResult()
        ;
    }
}

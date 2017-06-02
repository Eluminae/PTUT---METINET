<?php

namespace AppBundle\Services;

use AppBundle\Dtos\RealisationMarkDto;
use AppBundle\Models\Campaign;
use AppBundle\Models\Juror;
use AppBundle\Models\Mark;
use AppBundle\Models\Realisation;
use AppBundle\Models\UtcDate;
use Symfony\Component\Config\Definition\Exception\Exception;

class RealisationMarkFactory
{
    /** @var UuidGenerator */
    private $uuidGenerator;

    public function __construct(UuidGenerator $uuidGenerator)
    {
        $this->uuidGenerator = $uuidGenerator;
    }

    public function create(RealisationMarkDto $markDto)
    {
        return new Mark(
            $this->uuidGenerator->generateUuid(),
            new UtcDate($this->uuidGenerator->generateUuid(), new \DateTimeImmutable('now')),
            $markDto->value,
            $markDto->identity,
            $markDto->realisation
        );
    }
}

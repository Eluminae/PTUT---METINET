<?php

namespace AppBundle\Services;

use AppBundle\Dtos\RealisationMarkDto;
use AppBundle\Models\Mark;
use AppBundle\Models\UtcDate;

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

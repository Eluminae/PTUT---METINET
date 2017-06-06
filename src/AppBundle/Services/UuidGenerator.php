<?php

namespace AppBundle\Services;

use Ramsey\Uuid\Uuid;

class UuidGenerator
{
    public function generateUuid(): string
    {
        return Uuid::uuid4()->toString();
    }
}

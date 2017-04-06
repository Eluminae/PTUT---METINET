<?php
/**
 * Created by PhpStorm.
 * User: corentinbouix
 * Date: 31/03/2017
 * Time: 11:09
 */

namespace AppBundle\Services;


use Ramsey\Uuid\Uuid;

class UuidGenerator
{
    public function generateUuid(): string
    {
        return Uuid::uuid4()->toString();
    }
}
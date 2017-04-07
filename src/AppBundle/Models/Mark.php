<?php

namespace AppBundle\Models;

use AppBundle\Models\Identity;
use AppBundle\Models\Realisation;
use AppBundle\Models\UtcDate;

class Mark
{
    private $id;
    private $date;
    private $value;
    private $identity;
    private $realisation;

    public function __construct(string $id, UtcDate $date, $value, Identity $identity, Realisation $realisation)
    {
        $this->id = $id;
        $this->date = $date;
        $this->value = $value;
        $this->identity = $identity;
        $this->realisation = $realisation;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getIdentity()
    {
        return $this->identity;
    }

    public function getRealisation()
    {
        return $this->realisation;
    }
}

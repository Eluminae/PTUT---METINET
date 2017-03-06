<?php

namespace AppBundle\Models;

class UtcDate
{
    private $date;

    public function __construct(\DateTimeImmutable $date)
    {
        $this->date = $date;
    }

    public function getDate()
    {
        return $this->date;
    }
}

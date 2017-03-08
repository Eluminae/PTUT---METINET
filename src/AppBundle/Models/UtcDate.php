<?php

namespace AppBundle\Models;

class UtcDate
{
    private $id;
    private $date;

    public function __construct(string $id, \DateTimeImmutable $date)
    {
        $this->id = $id;
        $this->date = $date;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDate()
    {
        return $this->date;
    }
}

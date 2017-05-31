<?php

namespace AppBundle\Models;

class Notation
{
    private $id;
    private $markType;
    private $markTypeNumber;

    const RANKING = 1;
    const NUMBER = 2;

    public function __construct(string $id, int $markType, int $markTypeNumber)
    {
        $this->id = $id;
        $this->markType = $markType;
        $this->markTypeNumber = $markTypeNumber;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getMarkType()
    {
        return $this->markType;
    }

    public function getMarkTypeNumber()
    {
        return $this->markTypeNumber;
    }
}
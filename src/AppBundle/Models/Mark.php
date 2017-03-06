<?php

namespace AppBundle\Models;

use AppBundle\Models\Juror;
use AppBundle\Models\Realisation;
use AppBundle\Models\UtcDate;

class Mark
{
    private $format;
    private $date;
    private $value;
    private $juror;
    private $realisation;

    public function __construct(string $format, UtcDate $date, $value, Juror $juror, Realisation $realisation)
    {
        $this->format = $format;
        $this->date = $date;
        $this->value = $value;
        $this->juror = $juror;
        $this->realisation = $realisation;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getJuror()
    {
        return $this->juror;
    }

    public function getRealisation()
    {
        return $this->realisation;
    }
}

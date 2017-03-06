<?php

namespace AppBundle\Models;

use AppBundle\Models\UtcDate;

class Campaign
{
    private $endDate;
    private $beginningDate;
    private $name;
    private $category;
    private $jurors;

    public function __construct(UtcDate $endDate, UtcDate $beginningDate, string $name, string $category, array $jurors)
    {
        $this->endDate = $endDate;
        $this->beginningDate = $beginningDate;
        $this->name = $name;
        $this->category = $category;
        $this->jurors = $jurors;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function getBeginningDate()
    {
        return $this->beginningDate;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function getJurors()
    {
        return $this->jurors;
    }
}

<?php

namespace AppBundle\Models;

use AppBundle\Models\UtcDate;

class Campaign
{
    private $id;
    private $endDate;
    private $beginningDate;
    private $name;
    private $category;
    private $description;
    private $imageUrl;

    public function __construct(string $id, UtcDate $endDate, UtcDate $beginningDate, string $name, string $category, string $description, string $imageUrl)
    {
        $this->id = $id;
        $this->endDate = $endDate;
        $this->beginningDate = $beginningDate;
        $this->name = $name;
        $this->category = $category;
        $this->description = $description;
        $this->imageUrl = $imageUrl;
    }

    public function getId()
    {
        return $this->id;
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

    public function getDescription()
    {
        return $this->description;
    }

    public function getImageUrl()
    {
        return $this->imageUrl;
    }
}

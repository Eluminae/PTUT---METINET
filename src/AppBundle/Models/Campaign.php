<?php

namespace AppBundle\Models;

use AppBundle\Models\UtcDate;

class Campaign
{
    const filePath = 'campaignImages';

    private $id;
    private $endDate;
    private $beginningDate;
    private $name;
    private $description;
    private $imageName;
    private $creator;
    /** @var ArrayCollection */
    private $jurors;

    public function __construct(string $id, UtcDate $endDate, UtcDate $beginningDate, string $name, string $description, string $imageName, Identity $creator)
    {
        $this->id = $id;
        $this->endDate = $endDate;
        $this->beginningDate = $beginningDate;
        $this->name = $name;
        $this->description = $description;
        $this->imageName = $imageName;
        $this->creator = $creator;
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

    public function getDescription()
    {
        return $this->description;
    }

    public function getImageName()
    {
        return $this->imageName;
    }

    public function getCreator()
    {
        return $this->creator;
    }

    public function getJurors()
    {
        return $this->jurors;
    }
}

<?php

namespace AppBundle\Models;

use AppBundle\Dtos\RealisationRegistration;
use AppBundle\Models\Campaign;
use Symfony\Component\HttpFoundation\File\File;
use AppBundle\Models\UtcDate;

class Realisation
{
    const filePath = 'realisationFiles';

    private $id;
    private $leftAt;
    private $name;
    private $fileName;
    private $campaign;
    private $candidates;
    private $averageMark;

    public function __construct(string $id, UtcDate $leftAt, string $name, Campaign $campaign, array $candidates)
    {
        $this->id = $id;
        $this->leftAt = $leftAt;
        $this->name = $name;
        $this->campaign = $campaign;
        $this->candidates = $candidates;
        $this->averageMark = $averageMark;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLeftAt()
    {
        return $this->leftAt;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function getFilePath()
    {
        return sprintf('%s/%s', self::filePath, $this->fileName);
    }

    public function getCampaign()
    {
        return $this->campaign;
    }

    public function getAverageMark()
    {
        return $this->averageMark;
    }

    public function updateAverageMark(float $averageMark)
    {
        $this->averageMark = $averageMark;
    }

    public function getCandidates()
    {
        return $this->candidates;
    }

    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }
}

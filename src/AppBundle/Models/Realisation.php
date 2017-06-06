<?php

namespace AppBundle\Models;



class Realisation
{
    const FILE_PATH = 'realisationFiles';

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
        $this->averageMark = 0;
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
        return sprintf('%s/%s', self::FILE_PATH, $this->fileName);
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

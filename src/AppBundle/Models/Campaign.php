<?php

namespace AppBundle\Models;

use Doctrine\Common\Collections\ArrayCollection;

class Campaign
{
    const FILE_PATH = 'campaignImages';

    const TO_BE_REVIEWED = 'to_be_reviewed';
    const ACCEPTED = 'accepted';
    const RESULTS_PUBLISHED = 'results_published';
    const CLOSED = 'closed';

    private $id;
    private $endDate;
    private $beginningDate;
    private $name;
    private $description;
    private $imageName;
    private $creator;
    private $notation;
    private $publicResults;

    /** @var ArrayCollection */
    private $jurors;
    private $status;

    public function __construct(string $id, UtcDate $endDate, UtcDate $beginningDate, string $name, string $description, string $imageName, Identity $creator, Notation $notation, bool $publicResults)
    {
        $this->id = $id;
        $this->endDate = $endDate;
        $this->beginningDate = $beginningDate;
        $this->name = $name;
        $this->description = $description;
        $this->imageName = $imageName;
        $this->creator = $creator;
        $this->status = self::TO_BE_REVIEWED;
        $this->notation = $notation;
        $this->publicResults = $publicResults;
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

    public function getImagePath()
    {
        return sprintf('%s/%s', self::FILE_PATH, $this->imageName);
    }

    public function getCreator()
    {
        return $this->creator;
    }

    public function getJurors()
    {
        return $this->jurors;
    }

    public function getNotation()
    {
        return $this->notation;
    }

    public function isOver()
    {
        return $this->getEndDate()->getDate() < new \DateTime('now') && self::ACCEPTED === $this->status;
    }

    public function approveCampaign()
    {
        $this->status = self::ACCEPTED;
    }

    public function isResultsPublic()
    {
        return $this->publicResults;
    }

    public function isActive()
    {
        if (
            self::ACCEPTED === $this->status &&
            !$this->isOver() &&
            $this->getBeginningDate()->getDate() < new \DateTime('now')
        ) {
            return true;
        }

        return false;
    }

    public function isResultsPublished()
    {
        if (self::RESULTS_PUBLISHED === $this->status) {
            return true;
        }

        return false;
    }

    public function close()
    {
        $this->status = self::CLOSED;
    }

    public function publishResults()
    {
        $this->status = self::RESULTS_PUBLISHED;
    }
}

<?php

namespace AppBundle\Models;

class File
{
    private $fileName;
    private $format;

    public function __construct(string $fileName, string $format)
    {
        $this->fileName = $fileName;
        $this->format = $format;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getFileName()
    {
        return $this->fileName;
    }
}

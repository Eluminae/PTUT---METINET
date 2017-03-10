<?php

namespace AppBundle\Models;

class Password
{
    private $id;
    private $password;

    public function __construct($id, $password)
    {
        $this->id = $id;
        $this->password = $password;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPassword()
    {
        return $this->password;
    }
}

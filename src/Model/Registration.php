<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as AppAssert;

class Registration {

    /**
     * @AppAssert\ContainsValidUsername(mode="strict")
     * @Assert\NotNull()
     */
    private $username;

    /**
     * @Assert\NotNull()
     */
    private $name;

    private $location;

    public function __construct()
    {

    }

    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getUsername() : ?string
    {
        return $this->username;
    }

    public function getName() : ?string
    {
        return $this->name;
    }

    public function setLocation(string $location)
    {
        $this->location = $location;
    }

    public function getLocation() : ?string
    {
        return $this->location;
    }

}
<?php

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Donation
{
    /**
     * @Assert\NotBlank()
     */
    public $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    public $email;

    /**
     * @Assert\NotBlank()
     */
    public $text;
}
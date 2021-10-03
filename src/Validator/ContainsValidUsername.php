<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsValidUsername extends Constraint
{
    public $message = 'custom.validation.already_registered'; //TODO: sent real email to template message
    public $mode = 'strict';

    public function validatedBy()
    {
        return static::class.'Validator';
    }
}
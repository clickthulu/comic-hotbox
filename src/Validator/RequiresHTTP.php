<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class RequiresHTTP extends Constraint
{

    public string $message = 'The URL "{{ url }}" needs to start with https:// or http://';
    public string $mode = 'strict';

    public function __construct(mixed $options = null, ?array $groups = null, mixed $payload = null)
    {
        parent::__construct($options, $groups, $payload);
    }


    public function __sleep(): array
    {
        return array_merge(
            parent::__sleep(),
            [
                'mode'
            ]
        );
    }

    // in the base Symfony\Component\Validator\Constraint class
    public function validatedBy(): string
    {
        return static::class.'Validator';
    }

}
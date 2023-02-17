<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class CPF extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'The value "{{ string }}" is not valid.';

    public function __construct(mixed $options = null, array $groups = null, mixed $payload = null, string $message = null)
    {
        parent::__construct($options, $groups, $payload);

        $this->message = $message ?? $this->message;
    }
}
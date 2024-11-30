<?php

namespace Yanah\LaravelKwik\Traits;

use Illuminate\Support\Str;

trait FieldTypeTrait
{
    private $defaultType = 'text';

    /**
     * Convert type from MySQL to html input
     */
    public function convertType(string $type) : string
    {
        $types = [
            'email'   => 'text',
            'varchar' => 'text',
            'text'    => 'textarea',
            'tinyint' => 'checkbox',
            'bool'    => 'checkbox',
            'boolean' => 'checkbox'
        ];

        return $types[$type] ?? $this->defaultType;
    }
}
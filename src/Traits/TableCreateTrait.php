<?php
namespace Yanah\LaravelKwik\Traits;

use InvalidArgumentException;

trait TableCreateTrait
{
    /**
     * PostCreate::class
     */
    public function crudCreateSetup()
    {
        if(!isset($this->crudSetup['create']))
        {
            throw new InvalidArgumentException('You have not specified any form.');
        }

        return app($this->crudSetup['create']);
    }

    /**
     * Get required fields to add asterisks
     */
    public function getRequiredFields() : array
    {
        $rules = $this->crudCreateSetup()
                      ->validationRules();

        $requiredFields = array_filter($rules, function ($rule) {
            return str_contains($rule, 'required');
        });

        return array_keys($requiredFields);
    }
}
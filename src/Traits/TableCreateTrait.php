<?php
namespace Yanah\LaravelKwik\Traits;

use InvalidArgumentException;

trait TableCreateTrait
{
    /**
     * Get required fields to add asterisks
     */
    public function getRequiredFields() : array
    {
        $rules = $this->setupCreate()
                      ->validationRules();

        $requiredFields = array_filter($rules, function ($rule) {
            return str_contains($rule, 'required');
        });

        return array_keys($requiredFields);
    }
}
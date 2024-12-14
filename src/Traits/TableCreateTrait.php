<?php
namespace Yanah\LaravelKwik\Traits;

use InvalidArgumentException;

trait TableCreateTrait
{
    private $indexOfUpdateCreate = [];

    /**
     * Get required fields to add asterisks
     */
    public function getRequiredFields() : array
    {
        $rules = $this->setupCreate()
                      ->getValidationRules();

        $requiredFields = array_filter($rules, function ($rule) {
            return str_contains($rule, 'required');
        });

        return array_keys($requiredFields);
    }

    /**
     * We may configure the updateCreate index
     */
    public function setIndexOfUpdateCreate(array $params)
    {
        $this->indexOfUpdateCreate = $params;
    }

    public function getIndexOfUpdateCreate()
    {
        return $this->indexOfUpdateCreate;
    }
}
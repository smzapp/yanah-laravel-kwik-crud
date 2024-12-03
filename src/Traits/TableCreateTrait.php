<?php
namespace Yanah\LaravelKwik\Traits;

trait TableCreateTrait
{
    /**
     * PostCreate::class
     */
    public function getCrudCreateSetup()
    {
        if(!isset($this->crudSetup['create']))
        {
            abort(500, 'You have not specified any form.');
        }

        return app($this->crudSetup['create']);
    }

    /**
     * Get required fields to add asterisks
     */
    public function getRequiredFields() : array
    {
        $rules = $this->getCrudCreateSetup()
                ->validationRules();

        $requiredFields = array_filter($rules, function ($rule) {
            return str_contains($rule, 'required');
        });

        return array_keys($requiredFields);
    }
}
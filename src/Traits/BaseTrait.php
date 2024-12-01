<?php
namespace Yanah\LaravelKwik\Traits;

use Illuminate\Support\Facades\Schema;

trait BaseTrait 
{
    private $exceptFields = [
        'id', 
        'updated_at', 
        'created_at'
    ];

    public function getTableName()
    {
        $model = $this->getModel();

        return $model->getTable();
    }

    /**
     * Retrieve all Model fields
     */
    public function getModelAllFields() : array
    {
        $table   = $this->getTableName();

        return Schema::getColumnListing($table);
    }

    /**
     * Retrieve fields and remove those with except
     */
    public function getFilteredFields()
    {
        return array_diff($this->getModelAllFields(), $this->exceptFields);   
    }

    /**
     * We decide to return filtered fields or those defined by developer
     */
    public function getTableHeaders()
    {
        $headers = $this->getFilteredFields();

        if(isset($this->crudSetup['list']))
        {
            $list = app($this->crudSetup['list'])->headers();

            if(is_array($list) && count($list))
            {
                $headers = $list;
            }
        }

        return array_slice($headers, 0, 5);
    }

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
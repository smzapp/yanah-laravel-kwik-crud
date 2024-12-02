<?php
namespace Yanah\LaravelKwik\Traits;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

trait BaseTrait 
{
    private $exceptFields = [
        'id', 
        'updated_at', 
        'created_at'
    ];

    public function getTableName(): string
    {
        $model = $this->getModel();

        return $model->getTable();
    }

    public function query() : Builder
    {
        $model = $this->getModel();

        return $model::query();
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
    public function getFilteredFields(): array
    {
        return array_diff($this->getModelAllFields(), $this->exceptFields);   
    }

    /**
     * We want to optimize the query result by selecting the response data from mysql query.
     */
    public function selectedFields() : array
    {
        return array_merge(['id'], $this->getTableHeaders());
    }

    /**
     * We decide to return filtered fields or those defined by developer
     */
    public function getTableHeaders(): array
    {
        $headers = $this->getFilteredFields();

        if(isset($this->crudSetup['list']))
        {
            $fields = $this->getActiveFields();

            if(is_array($fields) && count($fields))
            {
                $headers = $fields;
            }
        }

        return array_slice($headers, 0, 5);
    }

    /**
     * Specify the fields to be shown into the list
     */
    public function getActiveFields()
    {
        $list   = $this->getCrudListSetup();
            
        return $list->activeFields ?? [];
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

    /**
     * PostList::class
     */
    public function getCrudListSetup()
    {
        if(!isset($this->crudSetup['list']))
        {
            abort(500, 'You have not specified any list.');
        }

        return app($this->crudSetup['list']);
    }

    public function paginateCollection(Collection $collection, int $perPage = 10, ?int $currentPage = null)
    {
        $currentPage = $currentPage ?: (LengthAwarePaginator::resolveCurrentPage() ?: 1);

        $items = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();
    
        return new LengthAwarePaginator(
            $items, 
            $collection->count(), 
            $perPage,
            $currentPage, 
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }
}
<?php
namespace Yanah\LaravelKwik\Traits;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Common methods which may appear in CRUD
 */
trait BaseTrait 
{
    protected $exceptFields = [
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
}
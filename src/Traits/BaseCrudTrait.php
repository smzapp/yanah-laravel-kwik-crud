<?php
namespace Yanah\LaravelKwik\Traits;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Common methods which may appear in CRUD
 */
trait BaseCrudTrait 
{
    protected $exceptFields = [
        'id', 
        'updated_at', 
        'created_at'
    ];

    public function getRedirectTo()
    {
        return $this->redirectTo ?? '';
    }

    public function getTableName(): string
    {
        $model = $this->getModelInstance();

        return $model->getTable();
    }

    public function query() : Builder
    {
        $model = $this->getModelInstance();

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

    
    
    public function setShouldIncludeFillable(bool $tubag)
    {
        $this->shouldIncludeFillable = $tubag;
    }

    public function getShouldIncludeFillable()
    {
        return $this->shouldIncludeFillable;
    }
}
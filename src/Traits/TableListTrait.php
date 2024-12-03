<?php
namespace Yanah\LaravelKwik\Traits;
use Yanah\LaravelKwik\App\Contracts\BodyPaginatorInterface;
use Yanah\LaravelKwik\App\Contracts\BodyCollectionInterface;
use InvalidArgumentException;

trait TableListTrait
{
    /**
     * PostList::class
     */
    public function getCrudListSetup()
    {
        if(!isset($this->crudSetup['list']))
        {
            throw new InvalidArgumentException('You have not specified any list.');
        }

        return app($this->crudSetup['list']);
    }

    /**
     * Specify the fields to be shown into the list
     */
    public function getActiveFields()
    {
        return $this->getCrudListSetup()->activeFields ?? [];
    }

    /**
     * Retrieve fields and remove those with except
     */
    public function getFilteredFields(): array
    {
        return array_diff($this->getModelAllFields(), $this->exceptFields);   
    }

    /**
     * We decide to return filtered fields or those defined by developer
     */
    public function getTableFields(): array
    {
        $fields = $this->getActiveFields();

        $headers = (is_array($fields) && count($fields)) ? $fields : $this->getFilteredFields();
    
        return array_slice($headers, 0, count($fields) ?: 5);
    }
    
    /**
     * We want to optimize the query result by selecting the response data from mysql query.
     */
    public function selectedFields() : array
    {
        return array_merge(['id'], $this->getTableFields());
    }

    public function hasActiveFields()
    {
        return count($this->getActiveFields());
    }

    /**
     * We get from collection or pagination
     */
    public function getResponseQueryData()
    {
        $crudList = $this->getCrudListSetup();

        $query = $this->query();

        if($this->hasActiveFields())
        {
            $query = $query->select($this->selectedFields());
        }

        if($crudList instanceof BodyPaginatorInterface)
        {
            return $crudList->responseBodyPaginator($query);
        }

        if($crudList instanceof BodyCollectionInterface)
        {
            return $crudList->responseBodyCollection($query);
        }

        throw new InvalidArgumentException('
            Table List should have either responseBodyPaginator or responseBodyCollection
        ');
    }
}
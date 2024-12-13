<?php
namespace Yanah\LaravelKwik\Traits;
use Yanah\LaravelKwik\App\Contracts\BodyPaginatorInterface;
use Yanah\LaravelKwik\App\Contracts\BodyCollectionInterface;
use Yanah\LaravelKwik\App\Contracts\ControlCrudInterface;
use Yanah\LaravelKwik\Enums\ListTemplateViewEnum;
use InvalidArgumentException;

trait TableListTrait
{
    /**
     * Toggle display of search input
     */
    public function getShowSearch() : bool
    {
        return $this->setupList()->showSearch ?? true;
    }

    /**
     * Specify the fields to be shown into the list
     */
    public function getActiveFields() : array
    {
        $fields = array_keys($this->setupList()->activeFields);

        return  $fields ?? [];
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
        $crudList = $this->setupList();

        $query = $this->query();

        $inputQuery = request('q');
        if($inputQuery) {
            $query = $crudList->search($query, $inputQuery);
        }

        if($this->hasActiveFields())
        {
            $query = $query->select($this->selectedFields());
        }

        if($crudList instanceof BodyCollectionInterface && $this->isListItemView())
        {
            return $crudList->responseBodyCollection($query);
        }

        if($crudList instanceof BodyPaginatorInterface)
        {
            return $crudList->responseBodyPaginator($query);
        }

        throw new InvalidArgumentException('
            Table List should have either responseBodyPaginator or responseBodyCollection
        ');
    }

    public function isTableList()
    {
        return $this->setupList()->getListView() === ListTemplateViewEnum::TABLELIST;
    }

    public function isListItemView()
    {
        return $this->setupList()->getListView() === ListTemplateViewEnum::LISTITEM;
    }

    public function getControls()
    {
        $crudList = $this->setupList();

        if($crudList instanceof ControlCrudInterface) {
            return $crudList->toggleVisibility(
                app(\Yanah\LaravelKwik\Crud\CrudListControl::class)
            );
        }

        throw new InvalidArgumentException('Unable to locate toggleVisibility method.');
    }

    public function configureListView()
    {
       $crudList = $this->setupList();
       
       if($crudList->getListView() === ListTemplateViewEnum::TABLELIST && 
            !($crudList instanceof BodyPaginatorInterface))
       {
           throw new InvalidArgumentException('You cannot use ListTemplateViewEnum::TABLELIST on BodyCollectionInterface. 
                Use BodyPaginatorInterface instead.');
       }

       return  $crudList->getListView();
    }
}
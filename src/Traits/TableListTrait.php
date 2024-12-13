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

    public function getTableHeaders() : array
    {
        return $this->setupList()->assignTableHeaders();
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
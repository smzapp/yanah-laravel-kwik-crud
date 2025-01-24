<?php
namespace Yanah\LaravelKwik\Crud;

use Yanah\LaravelKwik\Enums\ListTemplateViewEnum;
use Yanah\LaravelKwik\App\Contracts\ControlCrudInterface;

/**
 * This handles the list of records in CRUD
 */
class KwikCrudList implements ControlCrudInterface {

    /**
     * Return default Index Template view for this List
     * @return TABLELIST | LISTITEM
     */
    public function getListView(): ListTemplateViewEnum
    {
        return ListTemplateViewEnum::TABLELIST; 
    }

    /**
     * See CrudListControl to see more toggle visibility
     */
    public function toggleVisibility(CrudListControl $control) : array
    {
        return $control->get()->toArray();
    }

    /**
     * Default headers
     */
    public function assignTableHeaders(): array
    {
        return [];
    }
}
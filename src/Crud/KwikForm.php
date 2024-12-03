<?php
namespace Yanah\LaravelKwik\Crud;

use Yanah\LaravelKwik\Services\FormService;
use Yanah\LaravelKwik\Services\FormFieldService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Yanah\LaravelKwik\Traits\FieldTypeTrait;
use Yanah\LaravelKwik\Traits\BaseCrudTrait;
use Yanah\LaravelKwik\Traits\TableCreateTrait;
use RuntimeException;

/**
 * This is the Gateway to the package form.
 */
abstract class KwikForm 
{
    use FieldTypeTrait, BaseCrudTrait, TableCreateTrait;

    protected $form;

    public function __construct()
    {
        $this->form = new FormService;
    }

    abstract public function validationRules(): array;

    abstract public function prepareForm(): void;

    
    public function getModelInstance()
    {
        if(! $this->model) {
            throw new RuntimeException('Model is not initialized.');
        }
        
        return app($this->model);
    }

    public function getArrayForm() : array
    {
        return $this->form->getFormList();
    }

    /**
     * We initialize the form 
     */
    public function generateAutoForm(): void
    {
        $columns = $this->getFilteredFields();

        foreach ($columns as $column) 
        {
            $type = Schema::getColumnType($this->getTableName(), $column);

            $this->form->addField($column, [
                'type'  =>  $this->convertType($type),
                'label' => Str::headline($column) 
            ]);
        }
    }
}
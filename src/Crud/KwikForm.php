<?php
namespace Yanah\LaravelKwik\Crud;

use Yanah\LaravelKwik\Services\FormService;
use Yanah\LaravelKwik\Services\FormFieldService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

abstract class KwikForm {
    use \Yanah\LaravelKwik\Traits\FieldTypeTrait;

    protected $form;

    public function __construct()
    {
        $this->form = new FormService;
    }

    abstract public function getModel();

    abstract public function validationRules(): array;

    abstract public function prepareForm(): void;

    public function getCompleteForm()
    {
        return $this->form->getFormList();
    }

    /**
     * We initialize the form 
     */
    public function bootModelForm(): void
    {
        $model   = $this->getModel();
        $table   = $model->getTable();
        $columns = Schema::getColumnListing($table);

        foreach ($columns as $column) {
            $type = Schema::getColumnType('posts', $column);

            $exluded = ['id', 'created_at', 'updated_at'];

            if(!in_array($column, $exluded)) {
                $this->form->addField($column, [
                    'type'  =>  $this->convertType($type),
                    'label' => Str::headline($column) 
                ]);
            }
        }
    }
}
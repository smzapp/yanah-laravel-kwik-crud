<?php
namespace Yanah\LaravelKwik\Crud;

use Yanah\LaravelKwik\Services\FormService;

abstract class KwikForm {
    
    protected $form;

    public function __construct()
    {
        $this->form = new FormService;
    }

    abstract public function validationRules(): array;

    abstract public function prepareForm(): void;

    public function getCompleteForm()
    {
        return $this->form->getFormList();
    }

    public function initializeForm()
    {
        dd($this->model); // fetch from the database
    }
}
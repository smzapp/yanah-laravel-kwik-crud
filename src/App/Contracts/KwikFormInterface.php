<?php
namespace Yanah\LaravelKwik\App\Contracts;


use Yanah\LaravelKwik\Services\CrudService;

interface KwikFormInterface {

    public function getValidationRules(): array;

    public function prepareCreateForm(): void;

    public function beforeStore(CrudService $crudService) : void;

    public function afterStore($response);
}
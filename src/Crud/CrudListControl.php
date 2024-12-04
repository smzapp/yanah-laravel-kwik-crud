<?php
namespace Yanah\LaravelKwik\Crud;

use Yanah\LaravelKwik\Enums\ListControlEnum;
use Illuminate\Support\Collection;

/**
 * CRUD List control
 */
class CrudListControl {

    private Collection $config;

    public function __construct()
    {
        $this->config = collect([
            'showSearch'    => true,
            'showPrintPdf'  => true,
            'showAddButton' => true,
            'actions' => [
                ListControlEnum::PREVIEW->value => true,
                ListControlEnum::EDIT->value    => true,
                ListControlEnum::DELETE->value  => true,
            ],
        ]);
    }

    public function get(): Collection
    {
        return $this->config;
    }

    public function set(string $key, mixed $value): void
    {
        $this->config->put($key, $value);
    }

    public function updateAction(string $action, bool $value): void
    {
        $this->config->get('actions')->put($action, $value);
    }
}
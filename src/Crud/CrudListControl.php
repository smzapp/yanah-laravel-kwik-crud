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
        $this->config = $this->items();
    }

    private function items(): Collection
    {
        return collect([
            'showSearchBar' => true,
            'showSearch'    => false, // Search is available only on Paginated or TableListView
            'showPrintPdf'  => false,
            'showAddButton' => true,
            'showListSummary' => true,
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
        $actions = $this->config->get('actions');
        $actions[$action] = $value;
        
        $this->config->put('actions', $actions);
    }
}
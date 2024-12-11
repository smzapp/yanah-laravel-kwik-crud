<?php
namespace Yanah\LaravelKwik\App\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Yanah\LaravelKwik\Crud\CrudListControl;

/**
 * We apply collection
 */
interface PageShowRenderInterface
{
    public function renderShowVue(Builder $query, $id);
}
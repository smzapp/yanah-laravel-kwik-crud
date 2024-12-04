<?php
namespace Yanah\LaravelKwik\App\Contracts;

use Yanah\LaravelKwik\Crud\CrudListControl;

/**
 * We may apply pagination on the table list
 */
interface ControlCrudInterface
{
    public function toggleVisibility(CrudListControl $control) : array;
}

<?php
namespace Yanah\LaravelKwik\App\Contracts;


/**
 * We may apply pagination on the table list
 */
interface PageAffixInterface
{
    public function definePages(): array;
}

<?php
namespace Yanah\LaravelKwik\App\Contracts;

use Yanah\LaravelKwik\Crud\KwikPageControl;

/**
 * We may apply pagination on the table list
 */
interface PageControlInterface
{
    public function definePageAccess(KwikPageControl $control) : KwikPageControl;
}

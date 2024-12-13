<?php
namespace Yanah\LaravelKwik\Traits;


use Yanah\LaravelKwik\Crud\KwikPageControl;
use Yanah\LaravelKwik\App\Contracts\PageControlInterface;

/**
 * Common methods which may appear in CRUD
 */
trait PageAccessTrait
{
    /**
     * This serves as a gate control
     */
    public function verifyPageAccess(): void
    {
        if($this instanceof PageControlInterface) {
            $this->definePageAccess(
                app(KwikPageControl::class)
            );
        }
    }
}
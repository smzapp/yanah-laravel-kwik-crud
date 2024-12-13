<?php
namespace Yanah\LaravelKwik\Crud;


class KwikPageControl {
    
    private $activeIndex;

    private $allowedPages;

    const OPERATIONS = [
        'list', 'show', 'create', 'edit',

        'store', 'update', 'destroy'
    ];


    /**
     * Give access to specific pages.
     */
    public function allowAccess($pages = [])
    {
        $this->allowedPages = $pages;
    }

    public function validateOperation(string $index)
    {
        $this->activeIndex = $index;

        if(! $this->isOperationAllowed()) {
            abort(422, 'Operation is invalid!');
        }

        if(! $this->isAccessAllowed()) {
            abort(403, 'Access denied.');
        }
    }

    private function isAccessAllowed()
    {
        return in_array($this->activeIndex, $this->allowedPages);
    }

    private function isOperationAllowed()
    {
        return in_array($this->activeIndex, static::OPERATIONS);
    }
}

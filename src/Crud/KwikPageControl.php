<?php
namespace Yanah\LaravelKwik\Crud;


class KwikPageControl {
    
    private $accessPage;

    const LIST_PAGE_INDEX   = 'list';
    const SHOW_PAGE_INDEX   = 'show';
    const CREATE_PAGE_INDEX = 'create';
    const EDIT_PAGE_INDEX   = 'edit';

    /**
     * Give access to specific pages.
     */
    public function allowAccess($pages = [])
    {
        $this->accessPage = $pages;
    }

    public function isListAllowed() : boolean
    {
        return in_array(self::LIST_PAGE_INDEX, $this->accessPage);
    }

    
    public function isShowAllowed() : boolean
    {
        return in_array(self::SHOW_PAGE_INDEX, $this->accessPage);
    }

    public function isCreateAllowed() : boolean
    {
        return in_array(self::CREATE_PAGE_INDEX, $this->accessPage);
    }
    
    public function isEditAllowed() : boolean
    {
        return in_array(self::EDIT_PAGE_INDEX, $this->accessPage);
    }

}

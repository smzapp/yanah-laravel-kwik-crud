<?php
namespace Yanah\LaravelKwik\Traits;

use Illuminate\Support\Str;

trait ConfigurationsTrait
{
    private $activeRoute;
    private $breadCrumb;
    private $pageWrapperItems;

    public function commonProps()
    {
        return [
            'breadCrumbs' => $this->getBreadCrumb(),
            'layout'      => $this->getLayout(),
            'activeRoute' => $this->getActiveRoute(),
            'pageText'    => $this->getPageText(),
            'controls'    => $this->crudService->getControls(),
            'pageWrapper' => $this->getPageWrapperItems()
        ];
    }

    public function setPageWrapperItems(array $items): void
    {
        $this->pageWrapperItems = $items;
    }

    public function getPageWrapperItems(): ?array
    {
        if($this->wrapperControl()) {
            return $this->wrapperControl();  // assigned in PageWrapperTrait
        }

        return $this->pageWrapperItems;
    }

    public function configureRoute($route)
    {
        $this->activeRoute = $route;
    }

    public function configureBreadCrumb($crumb)
    {
        $this->breadCrumb = $crumb;
    }

    public function getActiveRoute()
    {
        return $this->activeRoute;
    }

    public function setActiveRoute(string $route)
    {
        $this->activeRoute = $route;
    }

    public function getBreadCrumb()
    {
        return $this->breadCrumb;
    }

    public function getPageText()
    {
        $table = $this->crudService->getTableName();
        $table = str_replace("_", " ", $table);
        $singular = Str::of($table)->singular();

        return [
            'plural' => $table,
            'singular' => $singular,
            'title'  => ucwords($singular)
        ];
    }
}
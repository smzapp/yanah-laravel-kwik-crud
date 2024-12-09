<?php
namespace Yanah\LaravelKwik\Traits;

use Illuminate\Support\Str;

trait ConfigurationsTrait
{
    private $activeRoute;
    private $breadCrumb;

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

    public function getBreadCrumb()
    {
        return $this->breadCrumb;
    }

    public function getPageText()
    {
        $table = $this->crudService->getTableName();
        $singular = Str::of($table)->singular();

        return [
            'plural' => $table,
            'singular' => $singular,
            'title'  => ucwords($singular)
        ];
    }
}
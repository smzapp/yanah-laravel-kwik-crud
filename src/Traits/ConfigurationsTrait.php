<?php
namespace Yanah\LaravelKwik\Traits;

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
}
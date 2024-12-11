<?php
namespace Yanah\LaravelKwik\Traits;

trait MainTrait
{
    /**
     * Get the value of viewLayout.
     *
     * @return string
     */
    public function getLayout(): string
    {
        return $this->layout;
    }

    /**
     * Set the value of viewLayout.
     *
     * @param string $viewLayout
     * @return void
     */
    public function setLayout(string $viewLayout): void
    {
        $this->layout = $viewLayout;
    }

   
    /**
     * Get the value of viewLayout.
     *
     * @return string
     */
    public function getPageTitle()
    {
        return class_basename($this->model);
    }
}
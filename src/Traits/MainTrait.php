<?php
namespace Yanah\LaravelKwik\Traits;

trait MainTrait
{

    /**
     * Get the value of perPage.
     *
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * Set the value of perPage.
     *
     * @param int $perPage
     * @return void
     */
    public function setPerPage(int $perPage): void
    {
        $this->perPage = $perPage;
    }

    /**
     * Get the value of showPagination.
     *
     * @return bool
     */
    public function getShowPagination(): bool
    {
        return $this->showPagination;
    }

    /**
     * Set the value of showPagination.
     *
     * @param bool $showPagination
     * @return void
     */
    public function setShowPagination(bool $showPagination): void
    {
        $this->showPagination = $showPagination;
    }

    /**
     * Get the value of showPrint.
     *
     * @return bool
     */
    public function getShowPrint(): bool
    {
        return $this->showPrint;
    }

    /**
     * Set the value of showPrint.
     *
     * @param bool $showPrint
     * @return void
     */
    public function setShowPrint(bool $showPrint): void
    {
        $this->showPrint = $showPrint;
    }

    /**
     * Get the value of showPdfExport.
     *
     * @return bool
     */
    public function getShowPdfExport(): bool
    {
        return $this->showPdfExport;
    }

    /**
     * Set the value of showPdfExport.
     *
     * @param bool $showPdfExport
     * @return void
     */
    public function setShowPdfExport(bool $showPdfExport): void
    {
        $this->showPdfExport = $showPdfExport;
    }

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

    public function getPageTitle()
    {
        return class_basename($this->model);
    }
}
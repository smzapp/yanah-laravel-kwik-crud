<?php
namespace Yanah\LaravelKwik\Controllers;

use Yanah\LaravelKwik\Traits\MainTrait;
use InvalidArgumentException;

/**
 * Add Components: UI Fields, ImageUpload, 
 * 
 * Override these attributes in child classes
 */
abstract class KwikController extends BaseController 
{
    use MainTrait;

    /**
     * Has Maintrait
     */
    protected $layout = 'AuthenticatedLayout';

    protected $pageTitle = 'Home';
    
    protected $perPage = 15;

    protected $showPagination = true;

    protected $showPrint = true;

    protected $showPdfExport = true;

    public function getModel()
    {
        if($this->model  === null) {
            throw new InvalidArgumentException("Model is not set. Controller should have a \$this->model.");
        }
            
        return app($this->model);
    }
}
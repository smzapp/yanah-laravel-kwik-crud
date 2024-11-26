<?php
namespace Yanah\LaravelKwik\Controllers;

use Yanah\LaravelKwik\Traits\MainTrait;

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
    protected $viewLayout = 'AuthenticatedLayout';

    protected $pageTitle = 'Home';
    
    protected $perPage = 15;

    protected $showPagination = true;

    protected $showPrint = true;

    protected $showPdfExport = true;

}
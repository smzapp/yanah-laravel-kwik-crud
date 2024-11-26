<?php
namespace Yanah\LaravelKwik\Controllers;

use Yanah\LaravelKwik\Traits\MainTrait;

/**
 * Add Components: UI Fields, ImageUpload, 
 * 
 * Override these attributes in child classes
 */
class KwikController extends BaseController 
{
    use MainTrait;

    protected $viewLayout = 'AuthenticatedLayout';
    
    protected $perPage = 15;

    protected $showPagination = true;

    protected $showPrint = true;

    protected $showPdfExport = true;

}
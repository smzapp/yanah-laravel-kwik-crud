<?php
namespace Yanah\LaravelKwik\Controllers;

/**
 * Add Components: UI Fields, ImageUpload, 
 * 
 * Override these attributes in child classes
 */
class KwikController extends BaseController 
{
    protected $perPage = 15;

    protected $showPagination = true;

    protected $showPrint = true;

    protected $showPdfExport = true;
}
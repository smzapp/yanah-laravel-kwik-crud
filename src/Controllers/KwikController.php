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
    protected $layout = 'BaseCrudLayout';

    protected $pageTitle = 'Home';
    
    /**
     * Require route initialization
     */
    abstract public function configurations();
}
<?php
namespace Yanah\LaravelKwik\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Builder;
use Yanah\LaravelKwik\Services\CrudService;
use InvalidArgumentException;

/**
 * This BaseController mediates the System Controller and Laravel Facade Controller
 * Author: Samuel
 */
abstract class BaseController extends Controller
{
    protected $model;

    private $crudService;


    public function __construct(CrudService $service)
    {
        $this->crudService = $service;

        $this->crudService->initialize([

            'setup' => $this->getCrudSetup(),

            'model' => $this->getModel()
        ]);
    }

    /**
     * Property crudSetup must be initialized in Child controller
     */
    public function getCrudSetup()
    {
        if($this->crudSetup == null || ! is_array($this->crudSetup)) {
            throw new InvalidArgumentException('You must have a crudSetup in your controller.');
        }

        return $this->crudSetup;
    }

    /**
     * Model must initialized in Child Controller
     */
    public function getModel() : string
    {
        if($this->model  === null) {
            throw new InvalidArgumentException("Model is not set. Controller should have a \$this->model.");
        }
            
        return $this->model;
    }

    /**
     * Override Resource index 
     */
    public function index()
    {  
        $data = $this->crudService->getResponseQueryData();

        if (request()->wantsJson()) {
            return response()->json($data);
        }

        return Inertia::render('BaseCrud/Index', [
            'crud'      => $data,
            'layout'    => $this->getLayout(),
            'pageTitle' => $this->getPageTitle(),
            'fields'    => $this->crudService->getTableFields(),
            'routes'    => (object) $this->crudRoutes
        ]);
    }

    /**
     * Override Create method
     */
    public function create()
    {
        $childCreateForm = $this->crudService->setupCreate(); 

        $childCreateForm->prepareForm();

        return Inertia::render('BaseCrud/Create', [
            'pageTitle' => 'Create ' . $this->getPageTitle(),
            'formList'  => $childCreateForm->getArrayForm(),
            'layout'    => $this->getLayout(),
            'asterisks' => $this->crudService->getRequiredFields()
        ]);
    }
}
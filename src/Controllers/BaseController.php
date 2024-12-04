<?php
namespace Yanah\LaravelKwik\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Builder;
use Yanah\LaravelKwik\Services\CrudService;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;

/**
 * This BaseController mediates the System Controller and Laravel Facade Controller
 * Author: Samuel
 */
abstract class BaseController extends Controller
{
    protected $model;

    private $crudService;

    private $activeRoute;


    public function __construct(CrudService $service)
    {
        $this->crudService = $service;

        $this->initializeRoute();

        $this->crudService->initialize([

            'setup' => $this->getCrudSetup(),

            'modelInstance' => $this->getModelInstance()
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
    public function getModelInstance() : Model
    {
        if($this->model  === null) {
            throw new InvalidArgumentException("Model is not set. Controller should have a \$this->model.");
        }
            
        return app($this->model);
    }

    public function configureRoute($route)
    {
        $this->activeRoute = $route;
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
            'controls'  => $this->crudService->getControls(),
            'listview'  => $this->crudService->configureListView(),
            'layout'    => $this->getLayout(),
            'pageTitle' => $this->getPageTitle(),
            'tableName' => $this->crudService->getTableName(),
            'fields'    => $this->crudService->getTableFields(),
            'showSearch'  => $this->crudService->getShowSearch(),
            'activeRoute' => $this->activeRoute
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

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $this->getModelInstance()->findOrFail($id)->delete();

            DB::commit();

            return response()->json(['message' => 'Record deleted successfully.'], 200);

        } catch (\Exception $e) {

            DB::rollBack();
    
            return response()->json(['message' => 'Failed to delete record.'], 500);
        }
    }
}
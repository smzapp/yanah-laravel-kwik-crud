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
use Yanah\LaravelKwik\Traits\ConfigurationsTrait;

/**
 * This BaseController mediates the System Controller and Laravel Facade Controller
 * Author: Samuel
 */
abstract class BaseController extends Controller
{
    use ConfigurationsTrait;

    protected $model;
    private $crudService;

    public function __construct(CrudService $service)
    {
        $this->crudService = $service;

        $this->configurations();

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
            'tableName' => $this->crudService->getTableName(),
            'fields'    => $this->crudService->getTableFields(),
            'showSearch'  => $this->crudService->getShowSearch(),
            'layout'      => $this->getLayout(),
            'pageTitle'   => $this->getPageTitle(),
            'activeRoute' => $this->getActiveRoute(),
            'breadCrumb'  => $this->getBreadCrumb()
        ]);
    }

    /**
     * Override Create method
     */
    public function create()
    {
        $childCreateForm = $this->crudService->setupCreate(); 

        $childCreateForm->prepareForm();

        return Inertia::render('BaseCrud/CreateUpdate', [
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
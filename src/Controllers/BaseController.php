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

interface BaseInterface {
    
    public function getShowItem(Builder $query, $fields = ['*'], $id);

    public function configurations();
}

/**
 * This BaseController bridges the System Controller and Laravel Facade Controller
 * Author: Samuel Amador
 */
abstract class BaseController extends Controller implements BaseInterface
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
            'fields'    => $this->crudService->getTableFields(),
            'showSearch'  => $this->crudService->getShowSearch(),
            'pageText'    => $this->getPageText(),
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

        $childCreateForm->prepareCreateForm();

        return Inertia::render('BaseCrud/CreateEdit', [
            'pageText'   => $this->getPageText(),
            'formgroup'  => $childCreateForm->getArrayForm(),
            'layout'    => $this->getLayout(),
            'activeRoute' => $this->getActiveRoute(),
            'asterisks' => $this->crudService->getRequiredFields()
        ]);
    }

    /**
     * Resource show
     */
    public function show(string $id)
    {
        $model = $this->getModelInstance();

        try {
            $response = $this->getShowItem($model::query(), ['*'], $id); 
            return Inertia::render('BaseCrud/Show', [
                'pageText'    => $this->getPageText(),
                'layout'       => $this->getLayout(),
                'activeRoute' => $this->getActiveRoute(),
                'responseData' => $response
            ]);
        } catch(InvalidArgumentException $e) {
            abort(400, 'Unable to retrieve the appropriate record.');
        }
    }

    /**
     * Handle store
     */
    public function store(Request $request)
    {
        $childCreateForm = $this->crudService->setupCreate(); 

        $payload = $request->validate($childCreateForm->validationRules());

        // Set something before storing
        $childCreateForm->beforeStore($this->crudService);

        $model = $this->getModelInstance();

        $indexUpdateCreate = $this->crudService->getIndexOfUpdateCreate();

        if(is_array($indexUpdateCreate) && count($indexUpdateCreate) > 0) {
            $result = $model::updateOrCreate($indexUpdateCreate, $payload);
        } else {
            $result = $model::create($payload);
        }

        return $childCreateForm->afterStore($result);
    }

    /**
     * Override Edit method
     */
    public function edit(string $id)
    {
        $childEditForm = $this->crudService->setupEdit($id); 

        $model = $this->getModelInstance();

        $childEditForm->prepareEditForm($model::findOrFail($id));

        return Inertia::render('BaseCrud/CreateEdit', [
            'pageText'   => $this->getPageText(),
            'formgroup'  => $childEditForm->getArrayForm(),
            'layout'    => $this->getLayout(),
            'activeRoute' => $this->getActiveRoute(),
            'asterisks' => $this->crudService->getRequiredFields(),
            'button'    => [
                'text' => 'Save Changes'
            ]
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
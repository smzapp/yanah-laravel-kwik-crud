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
use Yanah\LaravelKwik\App\Contracts\PageShowRenderInterface;
use Yanah\LaravelKwik\Crud\CrudListControl;

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

    const MAIN_PAGE = 'BasePage';

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

        return Inertia::render(self::MAIN_PAGE, array_merge($this->commonProps(), [
            'crud'      => $data,
            'listview'  => $this->crudService->configureListView(),
            'fields'    => $this->crudService->getTableFields(),
            'pageTitle' => $this->getPageTitle(),
            'pageFile'  => 'CrudListPage'
        ]));
    }

    /**
     * Override Create method
     */
    public function create()
    {
        $childCreateForm = $this->crudService->setupCreate(); 

        $childCreateForm->prepareCreateForm();

        return Inertia::render(self::MAIN_PAGE, array_merge($this->commonProps(), [
            'formgroup'  => $childCreateForm->getArrayForm(),
            'asterisks' => $this->crudService->getRequiredFields(),
            'pageFile'  => 'CrudCreateEdit'
        ]));
    }

    /**
     * Resource show
     */
    public function show(string $id)
    {
        $model = $this->getModelInstance();

        try {
            if ($this instanceof PageShowRenderInterface) {
                return $this->renderShowVue($model::query(), $id);
            }

            $response = $this->getShowItem($model::query(), ['*'], $id); 
            
            return Inertia::render(self::MAIN_PAGE, array_merge($this->commonProps(), [
                'responseData' => $response,
                'activeId' => $id,
                'pageFile'  => 'CrudShowPage'
            ]));
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

        return Inertia::render(self::MAIN_PAGE, array_merge($this->commonProps(), [
            'formgroup'  => $childEditForm->getArrayForm(),
            'asterisks' => $this->crudService->getRequiredFields(),
            'activeId'  => $id,
            'button'    => [
                'text' => 'Save Changes'
            ],
            'pageFile'  => 'CrudCreateEdit'
        ]));
    }

    /**
     * Handle update
     */
    public function update(Request $request, string $id)
    {
        $childEditForm = $this->crudService->setupEdit($id); 

        $payload = $request->validate($childEditForm->validationRules());

        $model = $this->getModelInstance();

        $response = $model::where('id', $id)->update($payload);

        return $childEditForm->afterUpdate($response);
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
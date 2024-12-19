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
use Yanah\LaravelKwik\Crud\CrudListControl;
use Yanah\LaravelKwik\Traits\PageAccessTrait;
use Yanah\LaravelKwik\Traits\PageWrapperTrait;
use Yanah\LaravelKwik\Crud\KwikPageControl;
use Exception;

interface BaseInterface {
    
    public function getShowItem(Builder $query, $fields = ['*'], $id);

    public function configurations(): void;

    public function getPageControl(): KwikPageControl;
}

/**
 * This BaseController bridges the System Controller and Laravel Facade Controller
 * Author: Samuel Amador
 */
abstract class BaseController extends Controller implements BaseInterface
{
    use ConfigurationsTrait, PageAccessTrait, PageWrapperTrait;

    protected $model;

    private $crudService;

    private $pageControl;

    public function __construct(CrudService $service)
    {
        $this->crudService = $service;

        $this->pageControl = $this->getPageControl();

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
        $this->pageControl->validateOperation('list');

        $data = $this->crudService->getResponseQueryData();

        if (request()->wantsJson()) {
            return response()->json($data);
        }

        $setup = $this->crudService->setupList();

        $this->assignPageSetup($setup);

        return Inertia::render(static::MAIN_PAGE, array_merge($this->commonProps(), [
            'crud'      => $data,
            'listview'  => $this->crudService->configureListView(),
            'headers'    => $this->crudService->getTableHeaders(),
            'pageTitle' => $this->getPageTitle(),
            'pageFile'  => 'CrudListPage'
        ]));
    }

    /**
     * Override Create method
     */
    public function create()
    {
        $this->pageControl->validateOperation('create');

        $childCreateForm = $this->crudService->setupCreate(); 

        $this->assignPageSetup($childCreateForm);

        $childCreateForm->prepareCreateForm();

        return Inertia::render(static::MAIN_PAGE, array_merge($this->commonProps(), [
            'formgroup'  => $childCreateForm->getArrayForm(),
            'asterisks' => $this->crudService->getRequiredFields(),
            'pageFile'  => 'CrudCreateEdit',
            'redirectTo' => $childCreateForm->getRedirectTo()
        ]));
    }

    /**
     * Resource show
     */
    public function show(string $id)
    {
        $this->pageControl->validateOperation('show');

        $model = $this->getModelInstance();

        if(method_exists($this, 'restrictAccessByUuid')) {
            $this->restrictAccessByUuid($model, $id);
        }

        try {
            $selectables = array_merge(['id'], $model->getFillable());
            $response = $this->getShowItem($model::query(), $selectables, $id); 
            
            return Inertia::render(static::MAIN_PAGE, array_merge($this->commonProps(), [
                'responseData' => $response,
                'activeId' => $id,
                'uuid'     => $model::find($id)->uuid,
                'pageFile'  => 'CrudShowPage'
            ]));
        } catch(InvalidArgumentException $e) {
            abort(404, 'Unable to retrieve the appropriate record.');
        }
    }

    /**
     * Handle store
     */
    public function store(Request $request)
    {
        $this->pageControl->validateOperation('store');

        $childCreateForm = $this->crudService->setupCreate(); 
        $payload = $request->validate($childCreateForm->getValidationRules());

        $childCreateForm->beforeStore($this->crudService);

        $model = $this->getModelInstance();
        
        try {
            DB::beginTransaction();

            $indexUpdateCreate = $this->crudService->getIndexOfUpdateCreate();

            if(is_array($indexUpdateCreate) && count($indexUpdateCreate) > 0) {
                $result = $model::updateOrCreate($indexUpdateCreate, $payload);
            } else {
                $result = $model::create($payload);
            }

            DB::commit();
            
            return $childCreateForm->afterStore($result);

        }  catch (Exception $exception){
            DB::rollBack();
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    /**
     * Override Edit method
     */
    public function edit(string $id)
    {
        $this->pageControl->validateOperation('edit');

        $childEditForm = $this->crudService->setupEdit($id); 

        $this->assignPageSetup($childEditForm);

        $model = $this->getModelInstance();

        if(method_exists($this, 'restrictAccessByUuid')) {
            $this->restrictAccessByUuid($model, $id);
        }

        $childEditForm->prepareEditForm($model::findOrFail($id));

        return Inertia::render(static::MAIN_PAGE, array_merge($this->commonProps(), [
            'formgroup'  => $childEditForm->getArrayForm(),
            'asterisks' => $this->crudService->getRequiredFields(),
            'activeId'  => $id,
            'redirectTo' => $childEditForm->getRedirectTo(),
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
        $this->pageControl->validateOperation('update');

        $childEditForm = $this->crudService->setupEdit($id); 
        $payload = $request->validate($childEditForm->getValidationRules());
        $model   = $this->getModelInstance();

        try {
            DB::beginTransaction();
          
            $response = $model::findOrFail($id)->update($payload);

            DB::commit();
            return $childEditForm->afterUpdate($response);
        } catch (Exception $exception){
            DB::rollBack();
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }


    public function destroy($id)
    {
        $this->pageControl->validateOperation('destroy');
        
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
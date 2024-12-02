<?php
namespace Yanah\LaravelKwik\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Inertia\Inertia;
use Yanah\LaravelKwik\Traits\BaseTrait;
use Illuminate\Database\Eloquent\Builder;

interface BaseInterface {

    public function getTableName() : string;

    public function query(): Builder;

    public function getModelAllFields() : array;

    public function getFilteredFields(): array;

    public function selectedFields(): array;

    public function getTableHeaders(): array;

    public function getCrudCreateSetup();

    public function getRequiredFields() : array;
}

/**
 * This BaseController mediates the System Controller and Laravel Facade Controller
 * Author: Samuel
 */
abstract class BaseController extends Controller implements BaseInterface
{
    use BaseTrait;

    protected $model;


    public function index()
    {  
        if (request()->wantsJson()) {
            return response()->json($this->getData());
        }

        return Inertia::render('BaseCrud/Index', [
            'crud'      => $this->getData(),
            'layout'    => $this->getLayout(),
            'pageTitle' => $this->getPageTitle(),
            'fields'    => $this->getTableHeaders(),
            'routes'    => (object) $this->crudRoutes
        ]);
    }

    private function getData()
    {
        $childList = $this->getCrudListSetup();

        $query = $this->query()
                      ->select($this->selectedFields());

        $collection = $childList->responseBody($query);

        return $this->paginateCollection($collection, $this->getPerPage());
    }

    public function create()
    {
        $childCreateForm = $this->getCrudCreateSetup(); 

        $childCreateForm->prepareForm();

        return Inertia::render('BaseCrud/Create', [
            'pageTitle' => 'Create ' . $this->getPageTitle(),
            'formList'  => $childCreateForm->getArrayForm(),
            'layout'    => $this->getLayout(),
            'asterisks' => $this->getRequiredFields()
        ]);
    }
}
<?php
namespace Yanah\LaravelKwik\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Inertia\Inertia;
use Yanah\LaravelKwik\Traits\BaseTrait;
use Yanah\LaravelKwik\Traits\TableCreateTrait;
use Yanah\LaravelKwik\Traits\TableListTrait;
use Illuminate\Database\Eloquent\Builder;

/**
 * This BaseController mediates the System Controller and Laravel Facade Controller
 * Author: Samuel
 */
abstract class BaseController extends Controller
{
    use BaseTrait, TableCreateTrait, TableListTrait;

    protected $model;


    public function index()
    {  
        $data = $this->getResponseQueryData();

        if (request()->wantsJson()) {
            return response()->json($data);
        }

        return Inertia::render('BaseCrud/Index', [
            'crud'      => $data,
            'layout'    => $this->getLayout(),
            'pageTitle' => $this->getPageTitle(),
            'fields'    => $this->getTableFields(),
            'routes'    => (object) $this->crudRoutes
        ]);
    }

    public function create()
    {
        $childCreateForm = $this->crudCreateSetup(); 

        $childCreateForm->prepareForm();

        return Inertia::render('BaseCrud/Create', [
            'pageTitle' => 'Create ' . $this->getPageTitle(),
            'formList'  => $childCreateForm->getArrayForm(),
            'layout'    => $this->getLayout(),
            'asterisks' => $this->getRequiredFields()
        ]);
    }
}
<?php
namespace Yanah\LaravelKwik\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Inertia\Inertia;
use Yanah\LaravelKwik\Traits\BaseTrait;

/**
 * This BaseController mediates the System Controller and Laravel Facade Controller
 * Author: Samuel
 */
abstract class BaseController extends Controller
{
    use BaseTrait;

    protected $model;

    private function getData()
    {
        // We want to select the response data from mysql query.
        $fields = array_merge(['id'], $this->getTableHeaders());
        
        return $this->getModel()
                    ->select($fields)
                    ->paginate($this->getPerPage());
    }

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
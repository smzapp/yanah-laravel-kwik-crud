<?php
namespace Yanah\LaravelKwik\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Inertia\Inertia;

/**
 * This BaseController mediates the System Controller and Laravel Facade Controller
 * Author: Samuel
 */
abstract class BaseController extends Controller
{
    protected $model;

    // abstract protected function getValidationRules(): array;

    private function getData()
    {
        $object = (new $this->model);
        return $object->paginate($this->getPerPage());
    }

    public function index()
    {  
        if (request()->wantsJson()) {
            return response()->json($this->getData());
        }

        return Inertia::render('BaseCrud/Index', [
            'crud' => $this->getData(),
            'layout' => $this->getViewLayout(),
            'pageTitle' => $this->getPageTitle(),
            'fields' => ['title', 'body']
        ]);
    }

}
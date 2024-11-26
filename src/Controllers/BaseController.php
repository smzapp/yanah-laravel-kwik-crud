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


    public function index()
    {
        return Inertia::render('BaseCrud/Index', [
            'crud' => (new $this->model)->all(),
            'layout' => $this->getViewLayout()
        ]);
    }

}
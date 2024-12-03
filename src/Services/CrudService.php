<?php
namespace Yanah\LaravelKwik\Services;


use Yanah\LaravelKwik\Traits\BaseCrudTrait;
use Yanah\LaravelKwik\Traits\TableCreateTrait;
use Yanah\LaravelKwik\Traits\TableListTrait;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use RuntimeException;


class CrudService {
    use TableCreateTrait, TableListTrait, BaseCrudTrait;

    private $setup;
    private $model;

    public function initialize(array $config)
    {
        $this->setup = $config['setup'];

        $this->model = $config['model'];
    }

    public function getModelInstance() : Model
    {
        return app($this->model);
    }

    /**
     * PostCreate::class
     */
    public function setupCreate()
    {
        if(!isset($this->setup['create']))
        {
            throw new RuntimeException('You have not specified any form.');
        }

        return app($this->setup['create']);
    }

    /**
     * PostList::class
     */
    public function setupList()
    {
        if(!isset($this->setup['list']))
        {
            throw new RuntimeException('You have not specified any list.');
        }

        return app($this->setup['list']);
    }
}
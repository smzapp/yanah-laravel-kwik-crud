<?php
namespace Yanah\LaravelKwik\Services;


use Yanah\LaravelKwik\Traits\BaseCrudTrait;
use Yanah\LaravelKwik\Traits\TableCreateTrait;
use Yanah\LaravelKwik\Traits\TableEditTrait;
use Yanah\LaravelKwik\Traits\TableListTrait;
use Yanah\LaravelKwik\Traits\TableShowTrait;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use RuntimeException;


class CrudService {
    use TableCreateTrait, TableListTrait, TableEditTrait, BaseCrudTrait, TableShowTrait;

    private $setup;
    
    private $modelInstance;

    private $activeId;

    public function initialize(array $config)
    {
        $this->setup = $config['setup'];

        $this->modelInstance = $config['modelInstance'];
    }

    public function getModelInstance() : Model
    {
        return $this->modelInstance;
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
     * PostEdit::class
     */
    public function setupEdit($id)
    {
        if(!isset($this->setup['edit']))
        {
            throw new RuntimeException('You have not specified any Edit form.');
        }
        
        $this->activeId = $id;

        return app($this->setup['edit']);
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
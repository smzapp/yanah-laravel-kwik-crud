<?php
namespace Yanah\LaravelKwik\Traits;

trait FormGroupTrait
{
    private $activeModel;

    public function isFieldDisplay($fieldName = null)
    {
        if(!$fieldName)  {
            return false;
        }

        $except = ['id', 'uuid', 'created_at', 'updated_at'];
     
        return !in_array($fieldName, $except);
    }

    public function chooseType($fieldType = null) 
    {
        $type['tinyint'] = 'switch';

        return $type[$fieldType] ?? 'text';
    }

    /**
     * Setters
     */
    public function setValidationRules($rules)
    {
        $this->validations = $rules;
    }

    public function setActiveModel($model)
    {
        $this->activeModel = $model;
    }

    /**
     * Getters
     */
    public function getActiveModel()
    {
        return $this->activeModel;
    }

    public function getWrapKey()
    {
        return $this->wrapKey;
    }

    public function getTextHeadings()
    {
        return (object) $this->textHeadings;
    }

    public function getWrap()
    {
        return $this->wrap;
    }
}
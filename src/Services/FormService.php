<?php
namespace Yanah\LaravelKwik\Services;


class FormService {

    private $formList = [];

    /**
     * @param $type: string, $params: array
     */
    public function addField($fieldName, $attributes)
    {
        $this->formList[$fieldName] = $attributes;
    }

    public function getFormList()
    {
        return $this->formList;
    }
}
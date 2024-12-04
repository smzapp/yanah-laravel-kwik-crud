<?php
namespace Yanah\LaravelKwik\Services;

use InvalidArgumentException;

class FormGroupService {

    protected $groups = [];

    private $defaultDetails = [
        'tab' => false,
        'label' => '',
        'title' => '',
        'description' => '',
        'align' => 'left',
    ];

    /**
     * Add a new group
     *
     * @param bool $isGroup
     * @param array $details
     */
    public function addGroup(bool $isGroup, array $details): void
    {
        $this->groups[] = [
            'group' => $isGroup,
            'details' => array_merge($this->defaultDetails, $details),
            'fields' => [],
        ];
    }

    /**
     * Add a field to the last added group
     *
     * @param string $name
     * @param array $attributes
     */
    public function addField(string $name, array $attributes): void
    {
        if (empty($this->groups)) {

            $this->addGroup(true, []);

        } else {

            $lastGroupKey = array_key_last($this->groups);

            $this->groups[$lastGroupKey]['fields'][] = $attributes;
        }
    }

    /**
     * Get the final structured data
     *
     * @return array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }
}
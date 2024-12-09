<?php
namespace Yanah\LaravelKwik\Services;

use InvalidArgumentException;
use Illuminate\Support\Str;

class FormGroupService {

    protected $groups = [];

    private $defaultDetails = [
        'tab' => false,
        'label' => '',
        'title' => '',
        'description' => '',
        'align' => 'left',
    ];

    private $validations;

    public function __construct($rules)
    {
        $this->validations = $rules;
    }

    /**
     * Add a new group
     *
     * @param string $groupName
     * @param array $details
     */
    public function addGroup(string $groupName, array $details): void
    {
        $this->groups[] = [
            'group_name' => $groupName,
            'details'    => array_merge($this->defaultDetails, $details),
            'fields'     => [],
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
            $this->addGroup('primary', []);
        } else {

            $lastGroupKey = array_key_last($this->groups);

            $this->groups[$lastGroupKey]['fields'][$name] = $attributes;
        }
    }

    /**
     * Edit Details
     * 
     * @param string $groupName
     * @param array $details
     */
    public function editDetails(string $groupName, array $details): void
    {
        foreach ($this->groups as &$group) {
            if ($group['group_name'] === $groupName) {
                $group['defaults'] = $details;
                return;
            }
        }

        throw new InvalidArgumentException("Group '{$groupName}' does not exist.");
    }

    /**
     * Edit field
     * 
     * @param string $groupName
     * @param string $name
     * @param array $attributes
     */
    public function editField(string $groupName, string $name, array $attributes): void
    {
        foreach ($this->groups as &$group) {
            if ($group['group_name'] === $groupName) {
                if (! isset($group['fields'][$name])) {
                    throw new InvalidArgumentException("Field '{$name}' does not exist in group '{$groupName}'.");
                }
                
                $group['fields'][$name] = array_merge($group['fields'][$name], $attributes);
                return;
            }
        }

        throw new InvalidArgumentException("Group '{$groupName}' does not exist.");
    }

    /**
     * Get the final structured data
     *
     * @return array
     */
    public function getGroups(): array
    {
        $validations = $this->validations;

        //inject required from validationRules()
        $mapGroup = collect($this->groups)->map(function($item) use($validations) {
            $item['fields'] = collect($item['fields'])->map(function($field, $fieldName) use($validations) {
                if(isset($validations[$fieldName])) 
                {
                    $field['required'] = Str::contains($validations[$fieldName], 'required');
                }
                return $field;
            })->toArray();

            return $item;
        });

        return $mapGroup->toArray();
    }
}
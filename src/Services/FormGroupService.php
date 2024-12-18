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

    private $wrap    = null;
    private $wrapKey = '';

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

            if ($this->isWrap() === false) {
                $this->groups[$lastGroupKey]['fields'][$name] = $attributes;
            } else {
                if (!isset($this->groups[$lastGroupKey]['fields']['wrapperIndex'][$this->getWrapKey()])) {
                    $this->groups[$lastGroupKey]['fields']['wrapperIndex'][$this->getWrapKey()] = [
                        'vBind' => $this->getWrap()
                    ];
                }

                $this->groups[$lastGroupKey]['fields']['wrapperIndex'][$this->getWrapKey()]['wrappedItems'][$name] = $attributes;
            }
        }
    }


    /**
     * Wrap fields
     */
    public function beginWrap(string $inputIndex, array $bindProps)
    {
        $this->wrapKey = $inputIndex;

        $this->wrap = $bindProps;
    }

    public function endWrap()
    {
        $this->wrap = null;
        
        $this->wrapKey = null;
    }

    public function getWrapKey()
    {
        return $this->wrapKey;
    }

    public function getWrap()
    {
        return $this->wrap;
    }

    public function isWrap()
    {
        return (is_array($this->wrap) && count($this->wrap)) ? true : false;
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
                if (isset($group['fields'][$name])) {
                    $group['fields'][$name] = array_merge($group['fields'][$name], $attributes);
                    return;
                }

                if (isset($group['fields']['wrapperIndex'])) {

                    if($this->getWrap()) {
                        $wrappedBind = &$group['fields']['wrapperIndex'][$this->getWrapKey()]['vBind'] ?? null;
                        $wrappedBind = $this->getWrap(); // update the vBind
                    }

                    $wrapper = $group['fields']['wrapperIndex'];
                    foreach($wrapper as $wrapName => &$wrapGroup) {

                        if(isset($wrapGroup['wrappedItems'][$name])) {
                            $mergedAttributes = array_merge($wrapGroup['wrappedItems'][$name], $attributes); // restore the old and replace by new $attributes

                            $updatedWrap = &$group['fields']['wrapperIndex'][$wrapName]['wrappedItems'][$name];
                            $updatedWrap = $mergedAttributes;  // update the wrappedItems
                            return;
                        }
                    }
                }

                throw new InvalidArgumentException("Field '{$name}' does not exist in group '{$groupName}'.");
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

        // dd($mapGroup->toArray());
        return $mapGroup->toArray();
    }
}
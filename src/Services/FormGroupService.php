<?php
namespace Yanah\LaravelKwik\Services;

use InvalidArgumentException;
use Illuminate\Support\Str;
use Yanah\LaravelKwik\Traits\FormGroupTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * This manages the inputs of form whether to group 
 * it in tab or not. Feel free to customize your own fields
 */
class FormGroupService {
    use FormGroupTrait;

    private $defaultDetails = [
        'tab' => false,
        'label' => '',
    ];
    private $validations;
    protected $groups = [];
    private $wrap     = null;
    private $wrapKey  = '';
    private $tabIndex = 0;
    private $textHeadings = [];

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
            $this->addGroup('main_group', []);
        } 
        
        $lastGroupKey = array_key_last($this->groups);

        if ($this->isWrap() === false) {
            $attributes = array_merge([
                'tabIndex' => $this->tabIndex
            ], $attributes);

            $this->groups[$lastGroupKey]['fields'][$name] = $attributes;
        } else {
            if (!isset($this->groups[$lastGroupKey]['fields'][$this->getWrapKey()])) {
                $this->groups[$lastGroupKey]['fields'][$this->getWrapKey()] = [
                    'vBind' => $this->getWrap(),
                    'headings' => $this->getTextHeadings()
                ];
            }

            $this->groups[$lastGroupKey]['fields'][$this->getWrapKey()]['tabIndex'] = $this->tabIndex;
            $this->groups[$lastGroupKey]['fields'][$this->getWrapKey()]['wrappedItems'][$name] = $attributes;

        }

        $this->tabIndex++;
    }

    public function removeField(string $name): void
    {
        foreach ($this->groups as $groupKey => &$group) {
            // Case 1: Field exists directly in the group
            if (isset($group['fields'][$name])) {
                unset($group['fields'][$name]);
                return;
            }

            // Case 2: Field exists inside wrapped items
            if (isset($group['fields'][$this->getWrapKey()]['wrappedItems'][$name])) {
                unset($group['fields'][$this->getWrapKey()]['wrappedItems'][$name]);
                return;
            }
        }
    }

    public function removeFields(array $names): void
    {
        foreach ($names as $name) {
            $this->removeField($name);
        }
    }

    /**
     * Wrap fields
     */
    public function beginWrap(string $inputIndex, array $bindProps, array $options = [])
    {
        $this->wrapKey = $inputIndex;
        $this->wrap = $bindProps;
        $this->textHeadings = $options;
    }

    public function endWrap()
    {
        $this->wrap = null;
        $this->wrapKey = null;
        $this->textHeadings = [];
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

                $fields = isset($group['fields']) ? $group['fields'] : null;

                if ($fields) {
                    foreach ($group['fields'] as $wrapName => $wrapArray) {
                        
                        if($this->getWrap()) {
                            $wrappedBind = &$group['fields'][$this->getWrapKey()]['vBind'] ?? null;
                            $wrappedBind = $this->getWrap(); // update the vBind

                            $headings = &$group['fields'][$this->getWrapKey()]['headings'] ?? null;
                            $headings = $this->getTextHeadings(); // update the headings
                        }

                        if(isset($wrapArray['wrappedItems'][$name])) {
                            $mergedAttributes = array_merge($wrapArray['wrappedItems'][$name], $attributes); // restore the old and replace by new $attributes

                            $updatedWrap = &$group['fields'][$wrapName]['wrappedItems'][$name];
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

        return $mapGroup->toArray();
    }

    
   /**
     * Autoload edit data
     */
    public function initializeEdit($record)
    {
        if (!$record) {
            return;
        }

        $fillables = $record->getFillable();

        foreach ($this->groups as &$group) {
            if (!isset($group['fields']) || !is_array($group['fields'])) {
                continue;
            }

            $group['fields'] = $this->updateFields($group['fields'], $fillables, $record);
        }
    }

    private function updateFields(array $fields, array $fillables, $record): array
    {
        foreach ($fields as $field => &$groupField) {
            if (in_array($field, $fillables)) {
                $groupField['value'] = $record->$field ?? null;
            } elseif (isset($groupField['wrappedItems']) && is_array($groupField['wrappedItems'])) {
                $groupField['wrappedItems'] = $this->updateFields($groupField['wrappedItems'], $fillables, $record);
            }
        }

        return $fields;
    }


    /**
     * Create Form
     */
    public function autogenerateForm()
    {
        $fieldTypes = $this->activeModel->getColumnDetails();

        foreach($fieldTypes as $field) {
            $name = $field['name'] ?? '';
            $type = $field['type_name'] ?? '';

            if($this->isFieldDisplay($name)) {
                $this->addField($name, [
                    'type' => $this->chooseType($type),
                    'label' => str_replace('_', ' ', ucwords($name))
                ]);
            }
        }
    }
}
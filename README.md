# Yanah Laravel "Kwik" CRUD Package

## Description

This package is built to ease the work developers do by streamlining the process of scaffolding CRUD operations. It integrates seamlessly with Laravel, Inertia, and Vue.js 3, reducing boilerplate and simplifying the creation of CRUD functionality.

# Discord

To collaborate, please join here: https://discord.gg/UksAt4HqF9

## Overview
- Stack Used
- Installation & Configurations
- Run the application
- Package Command/s
- CRUD Implementation
> I. CRUD (Create) <br/>
> II. CRUD (LIST) <br/>
> III. CRUD (EDIT/UPDATE) <br/>
> IV. CRUD (SHOW)
- Customize Pages (CRUD)
> Insert Components before / after CRUD Pages (List, Edit, Create) <br/>
> Customize Form fields (Create/Edit)
- Additional Security
- CRUD Lifecycle
- Overriding CRUD controller method

## Stack Used

- [Inertia 2.0](https://inertiajs.com/)
- [Vue 3](https://vuejs.org/)
- [Prime Vue 4](https://primevue.org/vite/)
- Reactjs (Coming soon)

## Installation & Configurations

To install the package, follow these steps:
 
```bash
$ composer require yanah/laravel-kwik-crud
```

```bash
$ php artisan kwik:install
```
- The default scafold is vuejs. We'll use reactjs soon.
- `kwik:install --client=reactjs`, Install for reactjs (SOON)

Add `KwikServiceProvider` to the providers array in your `config/app.php`:

```php
'providers' => [
    // Other providers...
    Yanah\LaravelKwik\KwikServiceProvider::class,
]
```
<br/>

In `app/Http/Kernel.php`, ensure that the `HandleInertiaRequests` middleware is added under web middleware groups.

```php
protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\HandleInertiaRequests::class,
        // other middlewares...
    ],
];
```

Publish kwik configurations

`$ php artisan vendor:publish --tag=kwikconfig`

After this, new files will be added in `config`

Install Front-end dependencies:

`$ npm install vue@latest @fortawesome/fontawesome-free primevue @primevue/themes primeicons @primevue/forms` 


In `vite.config.js` alias, add `@kwik`

```javascript
resolve: {
    alias: {
        '@kwik': path.resolve(__dirname, 'vendor/yanah/laravel-kwik-crud/src/client/vuejs'), // use reactjs for react
    },
},
```

In `tailwind.config.js` alias, add this line.

```javascript
content: [
    // More contents here
    'vendor/yanah/laravel-kwik-crud/src/client/vuejs/**/*.vue',
],
```

## Run the application

Check `tailwind.config.js` configuration

> Make sure to implement or use `primevue` in `app.ts`
![Frontend Configurations](https://i.imgur.com/qKjalXk.png)

`$ npm run dev`

`$ php artisan serve`


## Package Commands

Execute CRUD automatically

```bash
$ php artisan kwik:crud {name} {--only=}
```

Check the flags below:

`name` - refers to your model name. It should be capitalized & in singular form.

`--only=`:
- `crudfiles` - Only the ModelList.php, ModelCreate.php, ModelEdit.php will be generated 
- `controller` - Only the {Model}Controller will be generated
- `model` - Only the Model will be generate

Example:
```bash
$ php artisan kwik:crud Post --only=crudfiles
```

## I. CRUD (Create)

Populate form with fields inside `prepareCreateForm()`:

Example:

```php
$this->formgroup->addGroup('users', [
    'tab' => true,
    'label' => 'Users',
]);

$this->formgroup->addField('first_name', [
    'label' => 'Samuel',
    'type' => 'text'
]);
```

<hr/>
In creating of a form, configure `Crud\{Model}Create.php`

### First, in prepareCreateForm(), Add group

Use the following syntax to add a group:

```php
$this->formgroup->addGroup('GROUP_NAME_UNIQUE', [
    'tab' => boolean,
    'label' => string,
]);
```

### Second, Add field. Here is the syntax:

```php
$this->formgroup->addField('FIELD_NAME', $attributes);
```

<b>Note:</b> The `type` attribute serves as the key to determine what input type we'll implement.

## $attributes (Properties)
 
<h2> Types: </h2>

- text
- input Group
- textarea
- switch
- checkbox
- radio
- select
- select_group
- calendar: date & time
- autocomplete
- custom_file
- custom_html 

```php
**Text** $attributes example:
[
    'label' => 'Post Title',
    'type' => 'text'
]

**Input Group** $attributes example:
[
    'type' => 'input_group',
    'label' => 'Address',
    'placeholder' => 'Type your address',
    'group_icon' => 'pi pi-address-book' 
]

- group_icon is referred here: https://primevue.org/icons/

**Textarea** $attributes example:
[
    'label' => 'Post Body',
    'type' => 'textarea',
    'rows' => 4
]
**Switch** $attributes example:
[
    'label' => 'Billing details',
    'type' => 'switch'
]

**Checkbox**  $attributes example:
[
    'type' => 'checkbox',
    'is_boolean' => true, // if you need to return the checkbox as boolean, else string.
    'value' => old('CHECKBOX_ITEM', false),
    'label' => 'Your label',
    'class_item' => 'mb-5'
]

**Radio** $attributes example:
[
    'label' => 'Business Options',
    'type' => 'radio',
    'options' => [
        ['label' => 'Option 1', 'value' => 'option1'],
        ['label' => 'Option 2', 'value' => 'option2'],
        ['label' => 'Option 3', 'value' => 'option3'],
    ]
]


**Select** $attributes example:
[
    'label' => 'Business category',
    'type' => 'select',
    'options' => [
        ['label' => 'Option 1', 'optionValue' => 'option1'], // note: optionValue should be string
        ['label' => 'Option 2', 'optionValue' => 'option2'],
        ['label' => 'Option 3', 'optionValue' => 'option3'],
    ]
]

**Select Group** $attributes example:
[
    'type' => 'select_group',
    'label' => 'Service Group',
    'placeholder' => 'List of Services',
    'required' => true,
    'options' => [
        [
            'label' => 'First group',
            'items' => [
                [ 'label' => 'First1', 'optionValue' => 'first1'],  // note: optionValue should be string
                [ 'label' => 'First2', 'optionValue' => 'first2'],
                [ 'label' => 'First3', 'optionValue' => 'first3'],
            ],
        ],
    ]
];

**Calendar** $attributes example:
[
    'label' => 'business Calendar',
    'type' => 'calendar'
]

To set time only, configure inputProps:
[
    'type' => 'calendar',
    'label' => 'Time only',
    'inputProps' => [
        'timeOnly' => true,
        'hourFormat' => '12'
    ]
]

See more attributes here: https://primevue.org/datepicker/#time

**Custom html**
- If you wan to add a custom html:
[
    'type' => 'custom_html',
    'value' => function() {
        return '<div class="text-3xl border-t pt-4 mb-5">Read here.</div>';
    }
]
```

## Autocomplete input

Example:

```php
[
    'type'    => 'autocomplete',
    'required' => true,
    'label'   => 'Search me',
    'default_query_results' => [
        ['label' => 'test', 'value' => 'this'],
        ['label' => 'test2', 'value' => 'this2'],
    ],
    'api_endpoint' => '/post/search'
]
```

| Property              | Type             | Description                                                                                   |
|-----------------------|------------------|-----------------------------------------------------------------------------------------------|
| `default_query_results` | `array`          | Automatically populates values to be searched if an API fetch is not required.               |
| `api_endpoint`         | `string \| null` | Defines the API endpoint. You should have a `/post/search` route to handle the request and then the response must match the data structure of `default_query_results`. |

## Creating custom vue file

Add custom vue file into the field.

```php
[
    'type'  => 'custom_file', 
    'source' => '@/Components/CustomVueFile.vue' // This is relative to resources/js/Components directory
]
```
- `source` - All fileds should be saved inside `Components` folder. 

Props:

`attributes` - All of the array values above will serve as attributes.

Emit

`@updateFieldValue` - This will update the value and be passed as payload.

Example:

```javascript
<template>
    <label>Input Something</label>
    <input
        @input="updateInput"
        :class="`border-gray-300 shadow-sm focus:ring-primary-default focus:border-primary-default p-2 w-full`"
    />
</template>
<script setup>
const props = defineProps({
    attributes: Object,
    fieldName: String
})
const emit = defineEmits(['updateFieldValue']);
function updateInput(event) {
    emit('updateFieldValue', props.fieldName, event.target.value);
}
</script>
```

**More Attributes:**

```php
[
    'helper_text' => 'Sample text',
    
    'value' => 'ANY', // we attached this for every field for edit page purposes.

    'wrapperProps' => [
        // We may add bind to the wrapper of label & input
        // example: 'class' => 'bg-danger flex-row'
    ],

    'labelProps' => [
        // Cutomize or add styles on label
        // example: 'class' => 'text-sm'
    ],

    'inputProps' => [
        // Cutomize or add styles on Input Fields
        // example: 'class' => 'bg-danger w-full'
    ],

    'tooltip_label' => 'You may want to add tooltip for label. Icon is question mark.'
]
```
<small>(To customize fields proceed to the bottom.)</small>

*** Validations ***

Validations are defined in `$validationRules`.

```php
protected $validationRules = [
    // Add validations here.
];
```

## II. CRUD (LIST)

CRUD List is configured in `Crud\{Model}List.php`

### A. Two options how we display the table

**First**, Through Pagination.

Use `BodyPaginatorInterface` as interface, then add the `responseBodyPaginator()`.
<br/>
It should look like this:

```php
class {Model}List implements ControlCrudInterface, BodyPaginatorInterface
{
    public function responseBodyPaginator(Builder $query) : LengthAwarePaginator
    {
        // you may customize the query here.
        return $query->paginate($this->perPage);
    }  
}
```

**Second**, We may want to display all response data.

Use `BodyCollectionInterface` as interface, then add the `responseBodyCollection()`
<br/>
It should look like this:

```php
class {Model}List implements ControlCrudInterface, BodyCollectionInterface
{
    public function responseBodyCollection(Builder $query) : Collection
    {
        return $query->get()->map(function($item) {
            $item->primary = $item->title;  // customized main text
            $item->secondary = $item->body; // description
            return $item;
        });
    }   
}
```

or, you may customize the row using html codes by adding `rawHtml`.

```php
public function responseBodyCollection(Builder $query) : Collection
{
    return $query->get()->map(function($item) {
        $item->rawHtml = '<div class="text-xl">Custom html here</div>'; // Add this property
        return $item;
    });
}
```

### B. Define View

We have two options of how our list should look like:

`ListTemplateViewEnum::TABLELIST` or `ListTemplateViewEnum::LISTITEM`

- First, `TABLELIST` view contains pagination which requires `BodyPaginatorInterface` interface. <br />
- Second, `LISTITEM` view displays all list it is attached to `BodyCollectionInterface`.


### C. Toggle Visibility

See toggle buttons & texts we want to see in our CrudList page.

```php
public function toggleVisibility(CrudListControl $control) : array
{
    $control->set('showSearch', true); 
    $control->set('showPdf', true); // you can add more below

    return $control->get()->toArray();
}
```

To toggle action buttons:

```php
$control->updateAction('edit', true);
$control->updateAction('delete', true);
```

APIs:

- `showSearchBar`: boolean - wrapper of add button, search and summary
- `showSearch`: boolean - toggle display search input
- `showPrintPdf`: boolean - toggle pdf print button
- `showAddButton`: boolean - toggle Add button
- `showListSummary`: boolean - This shows the summary list of records in table list
- `actions`: array - toggle action buttons (preview, edit, delet). 

Available toggle controls:

```php
'showSearchBar' => true,
'showSearch'    => true,
'showPrintPdf'  => false,
'showAddButton' => true,
'showListSummary' => true,
'actions' => [
    'preview' => true,
    'edit' => true,
    'delete' => true,
]
```

### D. Handle Search functionality

Search functionality is visible only on pagination list and `$control->set('showSearch', true);`

```php
public function search(Builder $query, string $q) : Builder
{
    return $query->where('FIELD', $q);
}
```

## III. CRUD (EDIT/UPDATE)

We'll reuse the fields we defined in `prepareCreateForm()`.
<br/>

We update those in `prepareEditForm()`.

```php
$this->formgroup->editField('GROUP_NAME', 'FIELD_NAME', $attributes); // pattern
```

For `$attributes`, refer to  <b>$attributes (Properties)</b> above.

<br/>
Example:

```php
$this->formgroup->editField('details', 'post_title', [
    'label' => 'Post Title (Edited)',
    'value' => old('post_title', $post->title)
]);
```

Or the details:

```php
$this->formgroup->editDetails(string $groupName, array $details);
```

<small>(To customize fields proceed to the bottom.)</small>

*** Validations ***

Check method `getValidationRules` to modify validations added in `{Model}Create.php`

```php 
public function getValidationRules() {}
```

## IV. CRUD (SHOW)

From your controller, customize the display based on the data.

Expected return: 

```php
[
    'data' => $data, // This will be shown on the table
    'except' => [] // list down the fields you don't want to display.
];
```    

Customization:

You may add vue files before or after the table:

```php
public function getShowItem(Builder $query, $fields = ['*'], $id)
{
    $this->setPageWrapperItems([
        'prepend' => '@/Components/SampleLorem.vue', // Before the table
        'append'  => '@/Components/SampleLorem.vue', // This will be shown after the table
    ]);
    // More codes below
}
```

```javascript
<script setup>
const emit = defineEmits(['toggleShowTable']); // add this
</script>
```
<br/>

## Customize Pages (CRUD)

### Insert Components before / after CRUD Pages (List, Edit, Create)

Implement `PageAffixInterface` in `Crud\{Model}Create.php`, `Crud\{Model}Edit.php`, `Crud\{Model}List.php`
and define the components to be inserted (prepend / append).

```php
use Yanah\LaravelKwik\App\Contracts\PageAffixInterface;

class {CrudClass} extends KwikForm implements PageAffixInterface
{
    public function definePages(): array
    {
        return [
            'prepend' => '@/Components/YOUR_FILE_HERE.vue',
            'append'  => '@/Components/YOUR_FILE_HERE.vue'
        ];
    }
}
```

`prepend` & `append` have available api:

- `updateCrudList` -  Update list.

Example:

```javascript
//@/Components/YOUR_FILE_HERE.vue
const emit = defineEmits(['updateCrudList']); // Make sure to define updateCrudList.

const ChangeListItems = () => {
  router.visit(`URL`, {
    method: 'get',
    preserveState: true, 
    replace: true, 
    onSuccess: (response) => {
      emit('updateCrudList', response.props.crud)
    },
  });
}
```

## Customize Form fields (Create/Edit)

You may want to wrap fields.

Example:

```php
$this->formgroup->beginWrap('INDEX_KEY', $atributes, $headings);

// Add Fields here

$this->formgroup->endWrap();
```

See `$attributes` below: 

```php 
[
    'class' => 'gap-4 xs:grid-cols-1 sm:grid-cols-1', // cuztomize the cols number as desired
    'style' => 'background:red;'
]
```

See `$headings` (optional) below:

```php
[
    'heading' => string,
    'paragraph' => string,
]
```

## Additional Security

Notice that in `show` & `edit` pages, routes are accessible via id.

You may want to append `uuid` instead.

First, make sure to add `uuid` field in your model and migration.

Next, use the `UuidRestrictionTrait` trait in your controller.
<br/>
Example:

```php
use Yanah\LaravelKwik\Traits\UuidRestrictionTrait;

class {Your}Controller extends KwikController implements PageControlInterface
{
    use UuidRestrictionTrait; // Attach this
}
```

# CRUD Persist Lifecycle

`Store`
1. PageControl - Serves as middleware.
2. `beforeStore` - prepare method before storing.
3. Validations - handle validations.
4. Insert Model - updateOrCreate or create
5. `afterStore` - Handle . We may trigger an event after store.

`Update`
1. PageControl - Serves as middleware.
2. `beforeUpdate` - prepare method before storing.
3. Validations - handle validations.
4. Update model
5. `afterUpdate` - Handle . We may trigger an event after store.


# Overriding CRUD controller method

You may want to override Crud Controller methods
and use Laravel CRUD methods:

```php
class YourController extends KwikController  implements PageControlInterface
{
    public function create()
    {
        // do something here.

        return parent::create();
    }
}
```

<hr />

**Todo list:**

- Add Test
- Responsiveness
- Validate frontend

<br/>
That's all. Please feel free to send PR when you found a bug. 

Hope this package will help you "kwik"en your development. Appreciated!
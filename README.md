# Yanah Laravel "Kwik" CRUD Package

## Description

This package is built to ease the work developers do by streamlining the process of scaffolding CRUD operations. It integrates seamlessly with Laravel, Inertia, and Vue.js 3, reducing boilerplate and simplifying the creation of CRUD functionality.

## Requirements

- **Laravel Inertia**: This package is built on top of Inertia.js and Vue.js 3.
  - [Inertia.js Server-Side Setup](https://inertiajs.com/server-side-setup)

## Installation & Configurations

To install the package, follow these steps:
 
```bash
$ composer require yanah/laravel-kwik-crud
```
<br/>

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


## Run application

Check `tailwind.config.js` configuration

> Make sure to implement or use `primevue` in `app.ts`
![Frontend Configurations](https://i.imgur.com/Y3togIO.png)

`$ npm run dev`

`$ php artisan serve`


## Package Commands

> Autogenerate front-end CRUD files (To follow)

> Execute CRUD automatically

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

CRUD starts here. In creating of form, configure `Crud\{Model}Create.php`

### First, in prepareCreateForm(), Add group

Use the following syntax to add a group:

```php
$this->formgroup->addGroup('GROUP_NAME_UNIQUE', [
    'tab' => boolean,
    'label' => string,
    'title' => string,
    'description' => string,
    'align' => string,
]);
```

### Second, Add field. Here is the syntax:

```php
$this->formgroup->addField('FIELD_NAME', $attributes);
```

### API $attributes
 
<h2> Types: text, textarea, switch, radio, checkbox</h2>

```php
**Text** $attributes example:
[
    'label' => 'Post Title',
    'type' => 'text'
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

**Textarea** $attributes example:
[
    'label' => 'Post Body',
    'type' => 'textarea',
    'rows' => 4
]

**Select** $attributes example:
[
    'label' => 'Business category',
    'type' => 'select',
    'options' => ['Test1', 'Test2']
]

**Switch** $attributes example:
[
    'label' => 'Billing details',
    'type' => 'switch'
]
```

Example Form inside `prepareCreateForm()`:

```php
$this->formgroup->addGroup('users', [
    'tab' => true,
    'label' => 'Users',
    'title' => 'List of users',
    'description' => 'Display users',
    'align' => 'left',
]);

$this->formgroup->addField('first_name', [
    'label' => 'Samuel',
    'type' => 'text'
]);
```
**IMPORTANT:**
Make sure to add the fields in `validationRules()` you may want to be persisted. Add nullable for not required.

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

### B. Define View

We have two options of how our list should look like:

`ListTemplateViewEnum::TABLELIST` or `ListTemplateViewEnum::LISTITEM`

- First, `TABLELIST` view contains pagination which requires `BodyPaginatorInterface` interface. <br />
- Second, `LISTITEM` view displays all list it is attached to `BodyCollectionInterface`.


### C. Toggle Visibility

See `Yanah\LaravelKwik\Crud\CrudListControl` to *set* visibility methods.

```php
public function toggleVisibility(CrudListControl $control) : array
{
    $control->set('showSearch', true); 
    $control->set('showPdf', true); // you can add more below

    return $control->get()->toArray();
}
```

To toggle button actions:

```php
$control->updateAction('edit', true);
$control->updateAction('delete', true);
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


We may update `$attributes` in `prepareCreateForm()`
<br/>
example:

```php
$this->formgroup->editField('details', 'business_name', [
    'label' => 'Edited Business name',
    'value' => old('business_name', $post->body)
]);
```

**IMPORTANT:**
Make sure to add the fields in `validationRules()` you may want to be persisted. Add nullable for not required.

## IV. CRUD (SHOW)

In your controller, you have two options how you may display the `show` method:

First, using `getShowItem()` which will return Model record.

Second, using custom vuejs file. You have to implement `PageShowRenderInterface`

```php
/**
 * Custom vue for /{model}/{id} route
 */
public function renderShowVue(Builder $query, $id)
{
    // return Inertia
}
```
<br/>
<hr />
That's all. Please feel free to send PR when you found a bug. 

Hope this package will help you "kwik"en your development. Appreciated!
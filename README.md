# Yanah Laravel "Kwik" CRUD Package

## Description

This package is built to ease the work developers do by streamlining the process of scaffolding CRUD operations. It integrates seamlessly with Laravel, Inertia, and Vue.js 3, reducing boilerplate and simplifying the creation of CRUD functionality.

## Requirements

- **Laravel Inertia**: This package is built on top of Inertia.js and Vue.js 3.
  - [Inertia.js Server-Side Setup](https://inertiajs.com/server-side-setup)

## Installation

To install the package, follow these steps:
 
```bash
$ composer require yanah/laravel-kwik-crud
```
<br/>

Add KwikServiceProvider to the providers array in your `config/app.php`:

```php
'providers' => [
    // Other providers...
    Yanah\LaravelKwik\KwikServiceProvider::class,
]
```
<br/>
**(optional)** Add the following to your `composer.json` under the autoload section:

`"Yanah\\LaravelKwik\\": "packages/Yanah/LaravelKwik/src"`

Note: This is only for creating of package.
<br/>

In `app/Http/Kernel.php`, ensure that the HandleInertiaRequests middleware 

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

# Run application

`$ npm install vue@latest vue-easytable @fortawesome/fontawesome-free`

`$ npm install primevue @primevue/themes primeicons @primevue/forms` 
![primevue] https://primevue.org/vite/ & configure app.ts ![Configure](https://i.imgur.com/A5kDDjM.png)

`$ npm run dev`

`$ php artisan serve`

- Check tailwind.config.js configuration

> Make sure to implement or use `primevue` in `app.ts`
![Frontend Configurations](https://i.imgur.com/Y3togIO.png)


# I. CRUD (Create)

> CRUD List is configured in `Crud\{Model}Create.php`

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

<br/>

### Second, Add field. Here is the syntax:
<br/>

```php
$this->formgroup->addField('FIELD_NAME', $attributes);
```

<br/>

### API for addField $attributes
 
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

# II. CRUD (LIST)

> CRUD List is configured in `Crud\{Model}List.php`

### Two options how we display the table

**First**, Through Pagination.

Use `BodyPaginatorInterface` as interface, then add the `responseBodyPaginator()`
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

**Second**, We may want to display the entire response.

Use `BodyPaginatorInterface` as interface, then add the `responseBodyPaginator()`
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

## Toggle Visibility

> See `Yanah\LaravelKwik\Crud\CrudListControl` to *set* visibility methods.

# III. CRUD (UPDATE)

> We may update `$attributes` in `prepareCreateForm()`
<br/>
example:

```php
    $this->formgroup->editField('details', 'business_name', [
        'label' => 'Edited Business name',
        'value' => old('business_name', $post->body)
    ]);
```
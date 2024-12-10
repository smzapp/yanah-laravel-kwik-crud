# Yanah Laravel "Kwik" CRUD Package

## Description

This package is built to ease the work developers do by streamlining the process of scaffolding CRUD operations. It integrates seamlessly with Laravel, Inertia, and Vue.js 3, reducing boilerplate and simplifying the creation of CRUD functionality.

## Requirements

- **Laravel Inertia**: This package is built on top of Inertia.js and Vue.js 3.
  - [Inertia.js Server-Side Setup](https://inertiajs.com/server-side-setup)

## Installation

To install the package, follow these steps:

### 1. Install the Package

Run the following Composer command to install the package:


`$ composer require yanah/laravel-kwik-crud`


> Add EaseServiceProvider to the providers array in your `config/app.php`:

<code>
'providers' => [
    // Other providers...
    Yanah\LaravelKwik\EaseServiceProvider::class,
]
</code>

> Add the following to your `composer.json` under the autoload section:

`"Yanah\\LaravelKwik\\": "packages/Yanah/LaravelKwik/src"`

Note: This will be automated. php artisan vendor:publish --tag=config

> In `app/Http/Kernel.php`, ensure that the HandleInertiaRequests middleware 

<code>
protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\HandleInertiaRequests::class,
        // other middlewares...
    ],
];
</code>

`$ php artisan vendor:publish --tag=kwikconfig`

# Run

`$ php artisan serve`

`$ npm install vue@latest vue-easytable @fortawesome/fontawesome-free`

`$ npm install primevue @primevue/themes primeicons @primevue/forms` 
![primevue] https://primevue.org/vite/ & configure app.ts ![Configure](https://i.imgur.com/A5kDDjM.png)

`$ npm run dev`

- Check tailwind.config.js configuration

# CRUD (Create)

### First, in prepareCreateForm(), Add group

Use the following syntax to add a group:

```php
`$this->formgroup->addGroup('GROUP_NAME_UNIQUE', [
    'tab' => boolean,
    'label' => string,
    'title' => string,
    'description' => string,
    'align' => string,
]);`

<br/>
Second, Add field. Here is the syntax:
<br/>

```php
$this->formgroup->addField('FIELD_NAME', $attributes);

<br/>
**API** for addField $attributes
 
<h2> Types: text, textarea, switch, radio, checkbox</h2>

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
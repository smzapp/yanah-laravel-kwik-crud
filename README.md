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

```bash
composer require yanah/laravel-kwik-crud


> Add EaseServiceProvider to the providers array in your `config/app.php`:

'providers' => [
    // Other providers...
    Yanah\LaravelKwik\EaseServiceProvider::class,
]

> Add the following to your `composer.json` under the autoload section:

"Yanah\\LaravelKwik\\": "packages/Yanah/LaravelKwik/src"

Note: This will be automated. php artisan vendor:publish --tag=config

> In app/Http/Kernel.php, ensure that the HandleInertiaRequests middleware 

protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\HandleInertiaRequests::class,
        // other middlewares...
    ],
];

# Run

`$ php artisan serve`

`$ npm install vue@latest`
`$ npm run dev`
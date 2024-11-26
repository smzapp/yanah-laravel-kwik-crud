# Description
- This package is built to ease the so much works developers do.

# Requirement
- Laravel inertia
[text](https://inertiajs.com/server-side-setup)

# Installation

`composer require yanah/laravel-kwik-crud`

- Add `EaseServiceProvider` in `config/app.php` 

`'providers' => [
    // Other providers...
    Yanah\LaravelKwik\EaseServiceProvider::class,
]
`
- Add in composer.json 

`"Yanah\\LaravelKwik\\": "packages/Yanah/LaravelKwik/src"`

OR automate

`$ php artisan vendor:publish --tag=config`

//app/Http/Kernel.php
`protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\HandleInertiaRequests::class,
        // other middlewares...
    ],
];``


- Add this in `js/app.ts`

`resolve: (name) => {
    const pages = import.meta.glob<DefineComponent>('./Pages/**/*.vue');
    const otherDirectory = import.meta.glob<DefineComponent>('./Base/**/*.vue');

    const components = { ...pages, ...otherDirectory };

    if (components[`./${name}.vue`]) {
        return components[`./${name}.vue`]();
    }

    throw new Error(`Component "${name}" not found.`);
}`


# Run

`$ php artisan serve`

`$ npm run dev`
<?php

return [

    'app_name' => env('APP_NAME', 'Laravel Kwik App'),

    'base_url' => env('APP_URL', 'http://localhost'),

    'default_language' => 'en',

    'developer' => 'Samuel Amador',
    
    'namespaces' => [

        'crud' => 'App\\CrudKwik',

        'vue' => [
            
            'base_crud' => 'resources/js/Pages/BaseCrud',

            'root' => 'resources/js'
        ]
    ],

    'default_template' => null
];
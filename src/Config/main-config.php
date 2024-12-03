<?php

return [

    'app_name' => config('app.name', 'Laravel Kwik App'),

    'base_url' => url('/'),

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
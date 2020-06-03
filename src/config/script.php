<?php

return [

    'namespace'       => env('SCRIPT_COMPONENT_NAMESPACE', 'script-management'),

    'models'          => [
        'script' => VCComponent\Laravel\Script\Entities\Script::class,
    ],

    'transformers'    => [
        'script' => VCComponent\Laravel\Script\Transformers\ScriptTransformer::class,
    ],

    'viewModels'      => [

    ],
    'auth_middleware' => [
        'admin'    => [
            'middleware' => '',
            'except'     => [],
        ],
        'frontend' => [
            'middleware' => '',
            'except'     => [],
        ],
    ],

];

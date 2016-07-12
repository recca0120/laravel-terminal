<?php

return [
    'enabled'    => true,
    'whitelists' => [],
    'router'     => [
        'prefix'     => 'terminal',
        'as'         => 'terminal.',
        // if laravel 5.1 remove web
        'middleware' => 'web',
    ],
];

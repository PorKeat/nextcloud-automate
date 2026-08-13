<?php
return [
    'routes' => [
        ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
        ['name' => 'page#recentDocs', 'url' => '/api/recent', 'verb' => 'GET'],
        ['name' => 'page#createDoc', 'url' => '/api/create', 'verb' => 'POST'],
    ]
];

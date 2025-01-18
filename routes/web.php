<?php

use Illuminate\Support\Facades\Route;
use Elastic\Elasticsearch\ClientBuilder;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-elastic', function () {
    $client = ClientBuilder::create()->setHosts([env('ELASTICSEARCH_HOST')])->build();

    $params = [
        'index' => 'my_index',
        'body'  => [
            'query' => [
                'match' => [
                    'title' => 'Laravel'
                ]
            ]
        ]
    ];

    $response = $client->search($params);

    return response()->json($response);
});

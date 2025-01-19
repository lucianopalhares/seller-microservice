<?php

use Illuminate\Support\Facades\Route;
use Elastic\Elasticsearch\ClientBuilder;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index']);

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

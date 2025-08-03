<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// OpenAPI/Swagger UI
Route::get('/api/documentation', function () {
    return view('swagger-ui');
});

// OpenAPI JSON generation
Route::get('/api/openapi.json', function () {
    $openapi = \OpenApi\Generator::scan([
        app_path('Http/Controllers'),
        app_path('Http/Requests'),
        app_path('Models'),
    ]);
    
    return response($openapi->toJson())
        ->header('Content-Type', 'application/json');
});
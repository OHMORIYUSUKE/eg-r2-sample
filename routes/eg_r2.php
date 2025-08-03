<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::as('api')->group(function () {
    Route::controller('App\Http\Controllers\PostController')->group(function () {
        Route::get('/v1/posts','index');
        Route::post('/v1/posts','store');
        Route::get('/v1/posts/{id}','show');
        Route::put('/v1/posts/{id}','update');
        Route::delete('/v1/posts/{id}','destroy');
        Route::post('/v1/users/{user}/posts','createForUser');
    });
    Route::controller('App\Http\Controllers\UserController')->group(function () {
        Route::get('/v1/users','index');
        Route::post('/v1/users','store');
        Route::get('/v1/users/{id}','show');
        Route::put('/v1/users/{id}','update');
        Route::delete('/v1/users/{id}','destroy');
        Route::get('/v1/users/{id}/posts','posts');
    });
});

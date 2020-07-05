<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

// Register new user
Route::post('/register', 'UserController@register');
Route::post('/login', 'UserController@login');


Route::middleware('auth.bearer_token')->group(function() {
    // Work with current user
    Route::get('/user', 'UserController@show');
    Route::put('/user', 'UserController@update');
    Route::delete('/user', 'UserController@destroy');

    // Get all users
    Route::get('/users', 'UsersController@index');

    // Tasks
    Route::resource('/tasks', 'TasksController', ['only' => [
        'index', 'store', 'update', 'destroy'
    ]]);

    Route::get('/tasks/statuses', 'TasksStatusesController@index');

});

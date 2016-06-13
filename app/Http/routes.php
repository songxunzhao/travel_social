<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix'=> 'api', 'middleware' => ['api']], function () {
    // API routes
    Route::post('account/login', 'API\AuthController@login');
    Route::post('account/register', 'API\AuthController@register');
    Route::group(['middleware' => 'jwt.auth'], function() {
    	Route::resource('account/profile', 'API\ProfileController');
        Route::resource('events', 'API\EventController');
        Route::post('account/invites', 'API\InviteUserController@invite');
    	Route::post('files', 'API\FileController@upload');
        Route::get('events/{eventId}/attend', 'API\EventController@attend');
    });
});

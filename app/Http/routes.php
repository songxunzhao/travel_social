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

Route::get('/','HomeController@index');
//Route::get('/','HomeController@store');

Route::group(['prefix'=> 'api', 'middleware' => ['api']], function () {
    // API routes
    Route::post('account/login', 'API\AuthController@login');
    Route::post('account/register', 'API\AuthController@register');
    Route::resource('account/request', 'API\AccountRequestController');
    Route::post('account/createprofile', 'API\AuthController@create_profile');
    Route::post('account/password/reset', 'API\AccountRequestController@resetPassword');

    Route::group(['middleware' => 'jwt.auth'], function() {
    	Route::resource('account/profile', 'API\ProfileController');
        Route::post('account/invites', 'API\InviteUserController@invite');
	Route::post('account/profile/image', 'API\ProfileController@image');
        Route::resource('users', 'API\UserController');
	Route::resource('meetups', 'API\MeetUpController');
       	Route::resource('sos', 'API\SosController');
	Route::resource('events', 'API\EventController');
        Route::resource('events/store', 'API\EventController@store');
        Route::resource('events.members', 'API\EventMemberController');
	Route::resource('guides', 'API\GuideController');
	Route::resource('meetups/nearby', 'API\MeetUpController@getUsers');
	Route::resource('chats', 'API\ChatController');
	Route::resource('newsfeed', 'API\NewsFeedController');
	Route::post('chats/read', 'API\ChatController@read');
	Route::post('chats/unstar', 'API\ChatController@unstar');
	Route::get('chats/starred', 'API\ChatController@starred');
	Route::get('chats/inbox/admin', 'API\ChatController@admin');
	Route::get('chats/{email}/getall', 'API\ChatController@getall');
	Route::resource('notifications', 'API\NotificationController');
	Route::resource('chats/inbox', 'API\ChatController@sent');
	Route::post('chats/star', 'API\ChatController@star');
        Route::get('events/{eventId}/attend', 'API\EventController@attend');
        Route::get('events/{eventId}/cancel_attend', 'API\EventController@cancelAttend');
    	Route::post('files', 'API\FileController@upload');
    });
});

Route::auth();
Route::post('/add','HomeController@store');
Route::get('/addguide',function(){
	return view('addguide');
});
Route::get('/add','HomeController@index');

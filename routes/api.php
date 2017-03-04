<?php

use Illuminate\Http\Request;

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

Route::group(['middleware' => ['authentication']], function(){

	Route::get('/auth/logout', 'UserController@logout');

	Route::get('/auth/current', 'UserController@current');
	Route::patch('/auth/current', 'UserController@update');

	Route::post('/chats', 'ChatController@create');
	Route::get('/chats', 'ChatController@show');
	Route::patch('/chats/{id}', 'ChatController@update');

	Route::post('/chats/{id}/chat_messages', 'MessageController@create');
	Route::get('/chats/{id}/chat_messages', 'MessageController@show');

});

Route::post('/users', 'UserController@create');

Route::post('/auth/login', 'UserController@login');

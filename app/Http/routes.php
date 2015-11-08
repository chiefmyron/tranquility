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

Route::get('/', 'WelcomeController@index');

// Backoffice authentication routes
Route::get('/administration/auth', ['middleware' => 'administration.guest', 'uses' => 'Administration\AuthController@index']);
Route::post('/administration/auth/login', ['middleware' => 'administration.guest', 'uses' => 'Administration\AuthController@login']);
Route::get('/administration/auth/forgot-password', ['middleware' => 'administration.guest', 'uses' => 'Administration\AuthController@forgotPassword']);
Route::post('/administration/auth/forgot-password', ['middleware' => 'administration.guest', 'uses' => 'Administration\AuthController@sendPassword']);
Route::get('/administration/auth/logout', 'Administration\AuthController@logout');
	
// Backoffice administration routes
Route::group(['prefix' => 'administration', 'middleware' => ['administration.auth', 'administration.noCache']], function() {
	// Main dashboard
	Route::get('/', 'Administration\HomeController@index');
	
	// People controller
	Route::get('/people', 'Administration\PeopleController@index');
	Route::post('/people', 'Administration\PeopleController@store');
	Route::get('/people/create', 'Administration\PeopleController@create');
	Route::get('/people/{id}', 'Administration\PeopleController@show');
	Route::get('/people/{id}/update', 'Administration\PeopleController@update');
	
	// Users controller
	Route::get('/users', 'Administration\UsersController@index');
	Route::post('/users', 'Administration\UsersController@store');
	Route::get('/users/create', 'Administration\UsersController@create');
	Route::get('/users/{id}', 'Administration\UsersController@show');
	Route::get('/users/{id}/update', 'Administration\UsersController@update');
});

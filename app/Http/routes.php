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


Route::get('/test', 'TestController@index');

// Backoffice authentication routes
Route::get('/administration/auth', ['middleware' => 'administration.guest', 'uses' => 'Administration\AuthController@index']);
Route::post('/administration/auth/ajax', ['middleware' => 'administration.guest', 'uses' => 'Administration\AuthController@loginAjax']);
Route::post('/administration/auth/login', ['middleware' => 'administration.guest', 'uses' => 'Administration\AuthController@login']);
Route::get('/administration/auth/forgot-password', ['middleware' => 'administration.guest', 'uses' => 'Administration\AuthController@forgotPassword']);
Route::post('/administration/auth/forgot-password', ['middleware' => 'administration.guest', 'uses' => 'Administration\AuthController@sendPassword']);
Route::get('/administration/auth/logout', 'Administration\AuthController@logout');
	
// Backoffice administration routes
Route::group(['prefix' => 'administration', 'middleware' => ['administration.auth', 'administration.noCache']], function() {
	// Main dashboard
	Route::get('/',                                  'Administration\HomeController@index');
	
	// People controller
	Route::get ('/people',                           'Administration\PeopleController@index');
	Route::post('/people',                           'Administration\PeopleController@store');
	Route::get ('/people/create',                    'Administration\PeopleController@create');
	Route::post('/people/delete',                    'Administration\PeopleController@delete');
    Route::get ('/people/confirm',                   'Administration\PeopleController@confirmAction');
    Route::post('/people/confirm',                   'Administration\PeopleController@confirmAction');
	Route::get ('/people/{id}',                      'Administration\PeopleController@show');
	Route::get ('/people/{id}/update',               'Administration\PeopleController@update');
    Route::get ('/people/{id}/user',                 'Administration\PeopleController@showUser');
    Route::post('/people/{id}/user',                 'Administration\PeopleController@storeUser');
    Route::get ('/people/{id}/user/create',          'Administration\PeopleController@createUser');
    
	// Users controller
	Route::get ('/users',                            'Administration\UsersController@listPeopleUsers');
	Route::post('/users',                            'Administration\UsersController@storePersonUser');
	Route::get ('/users/create',                     'Administration\UsersController@create');
	Route::get ('/users/{id}',                       'Administration\UsersController@showPersonUser');
	Route::get ('/users/{id}/update',                'Administration\UsersController@updatePersonUser');
    Route::get ('/users/{id}/update/password',       'Administration\UsersController@changePassword');
    Route::post('/users/{id}/update/password',       'Administration\UsersController@saveNewPassword');
    
    // Address controller
    Route::post('/address',                          'Administration\AddressController@store');
    Route::get ('/address/create/{category}',        'Administration\AddressController@create');
    Route::get ('/address/{category}/{id}/update',   'Administration\AddressController@update');
    Route::get ('/address/{category}/{id}/confirm',  'Administration\AddressController@confirm');
    Route::post('/address/{category}/{id}/delete',   'Administration\AddressController@delete');
    Route::get ('/address/{category}/{id}/primary',  'Administration\AddressController@makePrimary');
    Route::get ('/address/{id}/map',                 'Administration\AddressController@displayMap');
    
    // Tags controller
    Route::post('/tags',                             'Administration\TagsController@store');
    Route::get ('/tags/autocomplete',                'Administration\TagsController@autocomplete');
    Route::get ('/tags/{parentId}',                  'Administration\TagsController@index');
    Route::get ('/tags/{parentId}/update',           'Administration\TagsController@update');
    Route::get ('/tags/{parentId}/remove/{id}',      'Administration\TagsController@remove');
    
    // Settings controller
    Route::get ('/settings',                         'Administration\SettingsController@index');
});

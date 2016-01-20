<?php
	
/*
|--------------------------------------------------------------------------
| Application Breadcrumbs
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/	

////////////////////////////
// Administration section //
////////////////////////////

// Home
Breadcrumbs::register('admin.home', function($breadcrumbs) {
	$breadcrumbs->push(trans('administration.common_home'), action('Administration\HomeController@index'));
});

// People controller
Breadcrumbs::register('admin.people', function($breadcrumbs) {
	$breadcrumbs->parent('admin.home');
	$breadcrumbs->push(trans('administration.people_heading_people'), action('Administration\PeopleController@index'));
});
Breadcrumbs::register('admin.people.show', function($breadcrumbs, $person) {
	$breadcrumbs->parent('admin.people');
	$breadcrumbs->push($person->firstName.' '.$person->lastName, action('Administration\PeopleController@show', [$person->id]));
});
Breadcrumbs::register('admin.people.create', function($breadcrumbs) {
	$breadcrumbs->parent('admin.people');
	$breadcrumbs->push(trans('administration.people_heading_create'), action('Administration\PeopleController@create'));
});
Breadcrumbs::register('admin.people.update', function($breadcrumbs, $person) {
	$breadcrumbs->parent('admin.people.show', $person);
	$breadcrumbs->push(trans('administration.people_command_update'), action('Administration\PeopleController@update'));
});
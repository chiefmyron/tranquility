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
	$breadcrumbs->push($person->getFullName(), action('Administration\PeopleController@show', [$person->id]));
});
Breadcrumbs::register('admin.people.create', function($breadcrumbs) {
	$breadcrumbs->parent('admin.people');
	$breadcrumbs->push(trans('administration.people_heading_create'), action('Administration\PeopleController@create'));
});
Breadcrumbs::register('admin.people.update', function($breadcrumbs, $person) {
	$breadcrumbs->parent('admin.people.show', $person);
	$breadcrumbs->push(trans('administration.people_command_update'), action('Administration\PeopleController@update', [$person->id]));
});

// Accounts controller
Breadcrumbs::register('admin.accounts', function($breadcrumbs) {
	$breadcrumbs->parent('admin.home');
	$breadcrumbs->push(trans('administration.accounts_heading_accounts'), action('Administration\AccountsController@index'));
});
Breadcrumbs::register('admin.accounts.show', function($breadcrumbs, $account) {
	$breadcrumbs->parent('admin.accounts');
	$breadcrumbs->push($account->getFullName(), action('Administration\AccountsController@show', [$account->id]));
});
Breadcrumbs::register('admin.accounts.create', function($breadcrumbs) {
	$breadcrumbs->parent('admin.accounts');
	$breadcrumbs->push(trans('administration.accounts_heading_create'), action('Administration\AccountsController@create'));
});
Breadcrumbs::register('admin.accounts.update', function($breadcrumbs, $account) {
	$breadcrumbs->parent('admin.accounts.show', $person);
	$breadcrumbs->push(trans('administration.accounts_command_update'), action('Administration\AccountsController@update', [$account->id]));
});

// Settings breadcrumbs
Breadcrumbs::register('admin.settings', function($breadcrumbs) {
	$breadcrumbs->parent('admin.home');
	$breadcrumbs->push(trans('administration.settings_heading_dashboard'), action('Administration\SettingsController@index'));
});

// Users controller
Breadcrumbs::register('admin.settings.users', function($breadcrumbs) {
	$breadcrumbs->parent('admin.settings');
	$breadcrumbs->push(trans('administration.users_heading_users_people'), action('Administration\UsersController@index'));
});
Breadcrumbs::register('admin.settings.users.show', function($breadcrumbs, $user) {
	$breadcrumbs->parent('admin.settings.users');
	$breadcrumbs->push($user->getDisplayName(), action('Administration\UsersController@show', [$user->id]));
});
Breadcrumbs::register('admin.settings.users.update', function($breadcrumbs, $user) {
	$breadcrumbs->parent('admin.settings.users.show', $user);
	$breadcrumbs->push(trans('administration.users_command_update_user'), action('Administration\UsersController@update', [$user->id]));
});
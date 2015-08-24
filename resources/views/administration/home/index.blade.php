@extends('administration.app')

@section('breadcrumbs', Breadcrumbs::render('admin.home'))

@section('content')
	<h1>Dashboard</h1>
	<hr />

	<a href="{{ action('Administration\PeopleController@index') }}"><h3>People</h3></a>
@stop
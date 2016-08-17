@extends('administration.app')

@section('menu')
	@include('administration._partials.menu', ['active' => 'people'])
@stop

@section('heading')
	@include('administration._partials.heading', ['heading' => trans('administration.people_heading_people')])
@stop

@section('actionButton')
	{{ ActionButton::setPrimaryAction('people_heading_create', 'toolbar-add-new-person', action('Administration\PeopleController@create'), null, 'pencil') }}
	{!! ActionButton::render() !!}
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.people'))

@section('content')
	@include('administration.people._partials.panels.list-table', ['people' => $people])
@stop
@extends('administration.app')

@section('heading')
	@include('administration._partials.heading', ['heading' => trans('administration.people_heading_create')])
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.people.create'))

@section('content')
	{!! Form::open(['url' => 'administration/people']) !!}
	@include('administration.people._partials.form', ['submitButtonText' => 'Add new person'])
	{!! Form::close() !!}
@stop
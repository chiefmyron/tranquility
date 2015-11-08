@extends('administration.app')

@section('content')
	<h1>Add new person</h1>
	<hr />
	@include('administration.errors.list')
	
	{!! Form::open(['url' => 'administration/people']) !!}
	@include('administration.people._partials.form', ['submitButtonText' => 'Add new person'])
	{!! Form::close() !!}
@stop
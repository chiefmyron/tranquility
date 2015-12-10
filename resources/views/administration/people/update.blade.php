@extends('administration.app')

@section('sidebar')
	@include('administration._partials.sidebar', ['active' => 'people'])
@stop

@section('content')
	<h1>Update person</h1>
	<hr />
	@include('administration.errors.list')
	
	{!! Form::model($person, ['url' => 'administration/people']) !!}
	@include('administration.people._partials.form', ['submitButtonText' => 'Update person details'])
	{!! Form::hidden('id', $person->id) !!}
	{!! Form::close() !!}
@stop
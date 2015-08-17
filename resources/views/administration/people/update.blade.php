@extends('administration.app')

@section('content')
	<h1>Update person</h1>
	<hr />
	@include('administration.errors.list')
	
	{!! Form::model($person, ['url' => 'administration/people']) !!}
	@include('administration.people.partials.form', ['submitButtonText' => 'Update person details'])
	{!! Form::hidden('id', $person->id) !!}
	{!! Form::close() !!}
@stop
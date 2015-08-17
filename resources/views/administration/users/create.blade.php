@extends('administration.app')

@section('content')
	<h1>Create new user account</h1>
	<hr />
	@include('administration.errors.list')
	
	{!! Form::open(['url' => 'administration/users']) !!}
	@include('administration.users.partials.form', ['submitButtonText' => 'Create new user account'])
	{!! Form::close() !!}
@stop
@extends('administration.app')

@section('content')
	<h1>Update user account</h1>
	<hr />
	@include('administration.errors.list')
	
	{!! Form::model($user, ['url' => 'administration/users']) !!}
	@include('administration.users.partials.form', ['submitButtonText' => 'Update user account'])
	{!! Form::hidden('id', $user->id) !!}
	{!! Form::close() !!}
@stop
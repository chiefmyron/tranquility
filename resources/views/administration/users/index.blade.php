@extends('administration.app')

@section('content')
	<h1>Users</h1>
	<hr />
	@include('administration.errors.list')
	
	@foreach ($content as $user)
		<h2><a href="{{ action('Administration\UsersController@show', [$user->id]) }}">{{ $user->firstName.' '.$user->lastName}}</a></h2>
		<p>{{ $user->username }}</p>
	@endforeach
	
@stop
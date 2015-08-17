@extends('administration.app')

@section('content')
	<h1>People</h1>
	<hr />
	@include('administration.errors.list')
	
	@foreach ($content as $person)
		<h2><a href="{{ action('Administration\PeopleController@show', [$person->id]) }}">{{ $person->firstName.' '.$person->lastName}}</a></h2>
		<div class="body">{{$person->position}}</div>
	@endforeach
	
@stop
@extends('administration.app')

@section('content')
	<h1>{{$person->firstName.' '.$person->lastName}}</h1>
	<hr />
	
	<dl>
		<dt>Position:</dt>
		<dl>{{ $person->position }}</dl>	
	</dl>
	
@stop
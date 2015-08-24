@extends('administration.app')

@section('breadcrumbs', Breadcrumbs::render('admin.people.show', $person))

@section('content')
	<h1>{{$person->firstName.' '.$person->lastName}}</h1>
	<hr />
	
	<dl>
		<dt>Position:</dt>
		<dl>{{ $person->position }}</dl>	
	</dl>
	
@stop
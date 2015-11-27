@extends('administration.app')

@section('heading')
	@include('administration._partials.heading', ['heading' => $person->firstName.' '.$person->lastName])
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.people.show', $person))

@section('content')
	<dl>
		<dt>Position:</dt>
		<dl>{{ $person->position }}</dl>	
	</dl>
	
@stop

@section('toolbar')
	@include('administration.people._partials.toolbar-show')
@stop

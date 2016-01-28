@extends('administration.app')

@section('heading')
	@include('administration._partials.heading', ['heading' => $person->getFullName()])
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.people.show', $person))

@section('sidebar')
	@include('administration._partials.sidebar', ['active' => 'people'])
@stop

@section('content')
	<dl>
		<dt>Position:</dt>
		<dl>{{ $person->position }}</dl>	
	</dl>
	
@stop

@section('toolbar')
	@include('administration.people._partials.toolbars.show')
@stop

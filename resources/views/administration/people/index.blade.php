@extends('administration.app')

@section('breadcrumbs', Breadcrumbs::render('admin.people'))

@section('content')
	<div id="main-content-container">
	@include('administration.people.partials.index-detail', ['content' => $content])
	</div>
@stop

@section('toolbar')
	<div id="toolbar-container">
	@include('administration.people.partials.toolbar-index-detail')
	</div>
@stop

		

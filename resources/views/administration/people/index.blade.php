@extends('administration.app')

@section('breadcrumbs', Breadcrumbs::render('admin.people'))

@section('content')
	@include('administration.people._partials.index-'.$viewType, ['content' => $content])
@stop

@section('toolbar')
	@include('administration.people._partials.toolbar-index-'.$viewType)
@stop

		

@extends('administration.app')

@section('heading')
	@include('administration._partials.heading', ['heading' => trans('administration.people_heading_people')])
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.people'))

@section('sidebar')
	@include('administration._partials.sidebar', ['active' => 'people'])
@stop

@section('content')
	@include('administration.people._partials.index-'.$viewType, ['content' => $content])
@stop

@section('toolbar')
	@include('administration.people._partials.toolbar-index-'.$viewType)
@stop
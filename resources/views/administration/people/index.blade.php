@extends('administration.app')

@section('menu')
	@include('administration._partials.menu', ['active' => 'people'])
@stop

@section('heading')
	@include('administration._partials.heading', ['heading' => trans('administration.people_heading_people')])
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.people'))

@section('content')
	@include('administration.people._partials.index-'.$viewType, ['content' => $content])
@stop

@section('toolbar')
	@include('administration.people._partials.toolbars.index-'.$viewType)
@stop
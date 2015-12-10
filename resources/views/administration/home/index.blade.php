@extends('administration.app')

@section('heading')
	@include('administration._partials.heading', ['heading' => trans('administration.common_dashboard')])
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.home'))

@section('sidebar')
	@include('administration._partials.sidebar', ['active' => 'dashboard'])
@stop

@section('content')
	This will be a beautiful dashboard... one day.
@stop
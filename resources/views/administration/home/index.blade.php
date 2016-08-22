@extends('administration.app')

@section('menu')
	@include('administration._partials.menu', ['active' => 'dashboard'])
@stop

@section('heading')
	@include('administration._partials.heading', ['heading' => trans('administration.common_dashboard')])
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.home'))

@section('content')
	This will be a beautiful dashboard... one day.

	{{ phpinfo() }}
@stop
@extends('administration.app')

@section('menu')
	@include('administration._partials.menu', ['active' => 'search'])
@stop

@section('heading')
	@include('administration._partials.heading', ['heading' => trans('administration.search_heading_search')])
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.search'))

@section('content')
	@include('administration.search._partials.panels.form', ['query' => $query])
	@include('administration.search._partials.panels.results', ['query' => $query, 'resultSet' => $results])
@stop
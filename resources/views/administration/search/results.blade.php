@extends('administration.app')

@section('menu')
	@include('administration._partials.menu', ['active' => 'search'])
@stop

@section('heading')
	@include('administration._partials.heading', ['heading' => trans('administration.search_heading_search')])
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.search'))

@section('content')
	@include('administration.search._partials.panels.form', ['searchParams' => $searchParams])
	@if ($totalResults > 0)
		@include('administration.search._partials.panels.results', ['searchParams' => $searchParams, 'resultSet' => $results])
	@else
		<div class="search-results no-results">
			{{ trans('administration.search_message_no_results') }}
		</div>
	@endif
@stop
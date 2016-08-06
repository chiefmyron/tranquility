@extends('administration.app')

@section('menu')
	@include('administration._partials.menu', ['active' => 'people'])
@stop

@section('heading')
	@include('administration._partials.heading', ['heading' => trans('administration.people_heading_people')])
@stop

@section('actionButton')
	{{ ActionButton::setPrimaryAction('people_heading_create', 'toolbar-add-new-person', action('Administration\PeopleController@create'), null, 'pencil') }}
	@if ($viewType == 'list')
		{{ ActionButton::addLink('people_command_switch_view_detail', 'toolbar-switch-view-detail', action('Administration\PeopleController@index', ['view' => 'detail']), 'ajax', 'user') }}
	@else
		{{ ActionButton::addLink('people_command_switch_view_table', 'toolbar-switch-view-table', action('Administration\PeopleController@index', ['view' => 'list']), 'ajax', 'th-list') }}
	@endif
	{!! ActionButton::render() !!}
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.people'))

@section('content')
	@include('administration.people._partials.panels.list-'.$viewType, ['content' => $content])
@stop

@section('toolbar')
	@include('administration.people._partials.toolbars.index-'.$viewType)
@stop
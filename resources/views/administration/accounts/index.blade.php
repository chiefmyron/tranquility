@extends('administration.app')

@section('menu')
	@include('administration._partials.menu', ['active' => 'accounts'])
@stop

@section('heading')
	@include('administration._partials.heading', ['heading' => trans('administration.accounts_heading_accounts')])
@stop

@section('actionButton')
	{{ ActionButton::setPrimaryAction('accounts_heading_create', 'toolbar-add-new-account', action('Administration\AccountsController@create'), 'ajax', 'pencil', true, ["data-ajax-preload-target" => "modal"]) }}
	{!! ActionButton::render() !!}
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.accounts'))

@section('content')
	@include('administration.accounts._partials.panels.list-table', ['accounts' => $accounts])
@stop
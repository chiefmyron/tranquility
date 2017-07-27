@extends('administration.app')

@section('menu')
	@include('administration._partials.menu', ['active' => 'accounts'])
@stop

@section('heading')
    @include('administration._partials.heading', ['heading' => $account->name])
@stop

@section('actionButton')
    {{ ActionButton::setPrimaryAction('accounts_command_update', 'update-account-details', action('Administration\AccountsController@update', ['id' => $account->id]), 'ajax', 'pencil', true, ['data-ajax-preload-target' => 'modal']) }}
	{{ ActionButton::addLink('accounts_command_delete', 'toolbar-delete-record', action('Administration\AccountsController@confirmDelete', ['id' => $account->id, 'action' => 'delete']), 'ajax', 'trash', true, ['data-ajax-preload-target' => 'modal']) }}
	{!! ActionButton::render() !!}
@stop

@section('breadcrumbs')
	{!! Breadcrumbs::render('admin.accounts.show', $account) !!}
@stop

@section('content')
    @include('administration.accounts._partials.content.show', ['account' => $account])
@stop
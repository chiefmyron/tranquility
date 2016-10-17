@extends('administration.app')
<?php
$user = $person->getUserAccount();
$account = $person->getAccount();
?>

@section('menu')
	@include('administration._partials.menu', ['active' => 'people'])
@stop

@section('heading')
    @include('administration._partials.heading', ['heading' => $person->getFullName(true)])
@stop

@section('actionButton')
    {{ ActionButton::setPrimaryAction('people_command_update', 'update-person-details', action('Administration\PeopleController@update', ['id' => $person->id]), '', 'pencil', true) }}
	{{ ActionButton::addLink('people_command_delete', 'toolbar-delete-record', action('Administration\PeopleController@confirmAction', ['id' => $person->id, 'action' => 'delete']), 'ajax', 'trash', true, ['data-ajax-preload-target' => 'modal']) }}
	{!! ActionButton::render() !!}
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.people.show', $person))

@section('content')
    @include('administration.people._partials.content.show', ['person' => $person])
@stop
@extends('administration.app')
<?php
$user = $person->getUserAccount();
?>

@section('menu')
	@include('administration._partials.menu', ['active' => 'people'])
@stop

@section('heading')
    @include('administration._partials.heading', ['heading' => $person->getFullName(true)])
@stop

@section('actionButton')
    {{ ActionButton::setPrimaryAction('people_command_update', 'update-person-details', action('Administration\PeopleController@update', ["id" => $person->id]), '', 'pencil', true) }}
	{{ ActionButton::addLink('people_command_delete', 'toolbar-delete-record', action('Administration\PeopleController@confirmAction', ['id' => $person->id, 'action' => 'delete']), 'ajax', 'trash', true, ["data-ajax-preload-target" => "modal"]) }}
	{!! ActionButton::render() !!}
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.people.show', $person))

@section('content')
    <div class="row">
        <div class="col-lg-3 col-md-4 col-sm-4">
            @include('administration.people._partials.panels.person-details', ['person' => $person])
        </div>
        <div class="col-lg-9 col-md-8 col-sm-8">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#activity-feed" aria-controls="activity-feed" role="tab" data-toggle="tab">Activity feed</a></li>
                <li role="presentation"><a href="#contact-details" aria-controls="contact-details" role="tab" data-toggle="tab">Contact details</a></li>
                <li role="presentation"><a href="#related-items" aria-controls="related-items" role="tab" data-toggle="tab">Related items</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane" id="activity-feed">...</div>
                <div role="tabpanel" class="tab-pane" id="contact-details">
                    @include('administration.people._partials.panels.person-contact-details', ['person' => $person])
                </div>
                <div role="tabpanel" class="tab-pane" id="related-items">...</div>
            </div>
        </div>
    </div>
@stop
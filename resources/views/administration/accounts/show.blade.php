@extends('administration.app')

@section('menu')
	@include('administration._partials.menu', ['active' => 'accounts'])
@stop

@section('heading')
    @include('administration._partials.heading', ['heading' => $account->name])
@stop

@section('actionButton')
    {{ ActionButton::setPrimaryAction('people_command_update', 'update-person-details', action('Administration\AccountsController@update', ["id" => $account->id]), '', 'pencil', true) }}
	{{ ActionButton::addLink('people_command_delete', 'toolbar-delete-record', action('Administration\AccountsController@confirmDelete', ['id' => $account->id, 'action' => 'delete']), 'ajax', 'trash', true, ["data-ajax-preload-target" => "modal"]) }}
	{!! ActionButton::render() !!}
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.accounts.show', $account))

@section('content')
    <div class="row">
        <div class="col-lg-3 col-md-4 col-sm-4">
            @include('administration.accounts._partials.panels.account-details', ['account' => $account])
        </div>
        <div class="col-lg-9 col-md-8 col-sm-8">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#activity-feed" aria-controls="activity-feed" role="tab" data-toggle="tab">Activity feed</a></li>
                <li role="presentation"><a href="#contact-details" aria-controls="contact-details" role="tab" data-toggle="tab">Contact details</a></li>
                <li role="presentation"><a href="#related-people" aria-controls="related-people" role="tab" data-toggle="tab">People</a></li>
                <li role="presentation"><a href="#related-items" aria-controls="related-items" role="tab" data-toggle="tab">Related items</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane" id="activity-feed">...</div>
                <div role="tabpanel" class="tab-pane" id="contact-details">
                    @include('administration.people._partials.panels.person-contact-details', ['person' => $account])
                </div>
                <div role="tabpanel" class="tab-pane" id="related-people">
                    
                </div>
                <div role="tabpanel" class="tab-pane" id="related-items">...</div>
            </div>
        </div>
    </div>
@stop
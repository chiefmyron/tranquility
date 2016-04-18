@extends('administration.app')
<?php
$user = $person->getUserAccount();
?>

@section('menu')
	@include('administration._partials.menu', ['active' => 'people'])
@stop

@section('heading')
	@include('administration._partials.heading', ['heading' => $person->getFullName(true), 'subheading' => $person->position])
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.people.show', $person))

@section('content')
    <div class="row">
        <div class="col-sm-4">
            @include('administration.people._partials.panels.person-details', ['person' => $person])
        </div>
        <div class="col-sm-8">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#activity-feed" aria-controls="activity-feed" role="tab" data-toggle="tab">Activity feed</a></li>
                <li role="presentation"><a href="#related-items" aria-controls="related-items" role="tab" data-toggle="tab">Related items</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane" id="activity-feed">...</div>
                <div role="tabpanel" class="tab-pane" id="related-items">...</div>
            </div>
        </div>
    </div>
@stop

@section('toolbar')
	@include('administration.people._partials.toolbars.show')
@stop

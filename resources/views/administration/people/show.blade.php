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
    
    @include('administration.people._partials.panels.person-details', ['person' => $person])
    
    <div class="clearfix"></div>

    <br />
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#contact-details" aria-controls="contact-details" role="tab" data-toggle="tab">Contact details</a></li>
        <li role="presentation"><a href="#activity-feed" aria-controls="activity-feed" role="tab" data-toggle="tab">Activity feed</a></li>
        <li role="presentation"><a href="#related-items" aria-controls="related-items" role="tab" data-toggle="tab">Related items</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="contact-details">
            @include('administration.addresses._partials.panels.physical-address', ['addresses' => $person->getAddresses('physical'), 'parentId' => $person->id])
            @include('administration.addresses._partials.panels.phone-address', ['addresses' => $person->getAddresses('phone'), 'parentId' => $person->id])
            @include('administration.addresses._partials.panels.email-address', ['addresses' => $person->getAddresses('email'), 'parentId' => $person->id])
        </div>
        <div role="tabpanel" class="tab-pane" id="activity-feed">...</div>
        <div role="tabpanel" class="tab-pane" id="related-items">...</div>
    </div>
@stop

@section('toolbar')
	@include('administration.people._partials.toolbars.show')
@stop

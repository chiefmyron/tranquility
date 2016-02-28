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
        <div class="col-sm-2 profile-picture-container">
            <a href="#"><img class="profile-picture" src="/backend/images/user-avatar-default.png" alt="..."></a>
        </div>
        
        <div class="col-sm-5">
            <dl class="data-list">
                <dt>Title:</dt>
                <dd>{{ $person->title or '<i>None</i>'}}</dd>
                
                <dt>First name:</dt>
                <dd>{{ $person->firstName }}</dd>
                
                <dt>Last name:</dt>
                <dd>{{ $person->lastName }}</dd>
            </dl>
        </div>
        
        <div class="col-sm-5">
            Other stuff
        </div>
        
    </div>
    
    
    
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
        </div>
        <div role="tabpanel" class="tab-pane" id="activity-feed">...</div>
        <div role="tabpanel" class="tab-pane" id="related-items">...</div>
    </div>
@stop

@section('toolbar')
	@include('administration.people._partials.toolbars.show')
@stop

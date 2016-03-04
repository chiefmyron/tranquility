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
                <dt>{{ trans('administration.people_label_title') }}</dt>
                <dd>{{ $person->title or "&nbsp;" }}</dd>
                
                <dt>{{ trans('administration.people_label_first_name') }}</dt>
                <dd>{{ $person->firstName }}</dd>
                
                <dt>{{ trans('administration.people_label_last_name') }}</dt>
                <dd>{{ $person->lastName }}</dd>
            </dl>
        </div>
        
        <div class="col-sm-5">
            <dl class="data-list">
                <dt>{{ trans('administration.people_label_user_account') }}</dt>
                @if(is_null($user))
                <dd>
                    No user account
                    <p><a href="{{ action('Administration\UsersController@create') }}" class="ajax">{{ trans('administration.users_heading_create_user') }}</a></p>
                </dd>
                @else
                <dd><a href="{{ action('Administration\UsersController@showPersonUser', ['id' => $user->id]) }}">{{ $user->username }}</a></dd>
                
                <dt>{{ trans('administration.users_label_account_status') }}</dt>
                    @if($user->active)
                <dd><strong class="text-success">{{ trans('administration.users_status_active') }}</strong></dd>    
                    @else
                <dd><strong class="text-warning">{{ trans('administration.users_status_suspended') }}</strong></dd>
                    @endif
                @endif
                
            </dl>
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
            @include('administration.addresses._partials.panels.email-address', ['addresses' => $person->getAddresses('email'), 'parentId' => $person->id])
        </div>
        <div role="tabpanel" class="tab-pane" id="activity-feed">...</div>
        <div role="tabpanel" class="tab-pane" id="related-items">...</div>
    </div>
@stop

@section('toolbar')
	@include('administration.people._partials.toolbars.show')
@stop

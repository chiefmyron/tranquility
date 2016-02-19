@extends('administration.app')
<?php
$user = $person->getUserAccount();
?>

@section('sidebar')
	@include('administration._partials.sidebar', ['active' => 'people'])
@stop

@section('content')
<div class="media">
    <div class="media-left">
        <a href="#"><img class="media-object profile-picture" src="/backend/images/user-avatar-default.png" alt="..."></a>
    </div>

    <div class="media-body">
        <h1 class="media-heading text-capitalize">{{ $person->getFullName(true) }}</h1>
        <h4>{{ $person->position }}</h4>
        <br />
        @if ($user && $user->active)
        <p>{!! trans('administration.people_message_has_active_user_account', ['name' => $person->firstName, 'registeredDateTime' => DateTimeFormatter::longDateTime($user->registeredDateTime)]) !!}<br />
        <a href="{{ action('Administration\PeopleController@showUser', [$person->id]) }}" class="ajax">View details</a> | Suspend account | Delete account</p>
        @elseif ($user && !$user->active)
        <p>{{ trans('administration.people_message_has_suspended_user_account', ['name' => $person->firstName]) }}<br />
        View details | Activate account | Delete account</p>    
        @else
        <p>{{ trans('administration.people_message_no_user_account', ['name' => $person->firstName]) }}<br />
        Create new user account</p>
        @endif
    </div>
</div>
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
            @include('administration.addresses._partials.panels.physical-address', ['addresses' => $person->getPhysicalAddresses(), 'parentId' => $person->id])
        </div>
        <div role="tabpanel" class="tab-pane" id="activity-feed">...</div>
        <div role="tabpanel" class="tab-pane" id="related-items">...</div>
    </div>
@stop

@section('toolbar')
	@include('administration.people._partials.toolbars.show')
@stop

@extends('administration.app')

@section('heading')
	@include('administration._partials.heading', ['heading' => trans('administration.users_heading_users_record', ['name' => $user->getDisplayName()])])
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.settings.users.show', $user))

@section('sidebar')
	@include('administration._partials.sidebar', ['active' => 'users'])
@stop

@section('content')
<div class="row">
    <div class="col-md-6">
        <dl class="data-list">
            <dt>{{ trans('administration.common_username') }}</dt>
            <dd>{{ $user->username }}</dd>    
            
            <dt>{{ trans('administration.users_label_locale') }}</dt>    
            <dd>{{ trans('locales.'.$user->localeCode) }}</dd>
            
            <dt>{{ trans('administration.users_label_timezone') }}</dt>    
            <dd>{{ trans('timezones.'.$user->timezoneCode) }}</dd>
        </dl>
    </div>
    <div class="col-md-6">
        <dl class="data-list">
            <dt>{{ trans('administration.users_label_registered_date_long') }}</dt>    
            <dd>{!! DateTimeFormatter::longDateTime($user->registeredDateTime) !!}</dd>

            <dt>{{ trans('administration.users_label_account_status') }}</dt>
            @if($user->active)    
            <dd>{{ trans('administration.users_status_active') }}</dd>
            @else
            <dd class="text-danger">{{ trans('administration.users_status_suspended') }}</dd>
            @endif

            <dt>{{ trans('administration.users_label_security_group') }}</dt>    
            <dd>{{ $user->securityGroupId }}</dd>    
        </dl>
    </div>    
</div>
@stop

@section('toolbar')
	@include('administration.users._partials.toolbars.show')
@stop

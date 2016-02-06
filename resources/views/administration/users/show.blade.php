@extends('administration.app')

@section('heading')
	@include('administration._partials.heading', ['heading' => trans('administration.users_heading_users_record', ['name' => $user->getDisplayName()])])
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.settings.users.show', $user))

@section('sidebar')
	@include('administration._partials.sidebar', ['active' => 'users'])
@stop

@section('content')
    @include('administration.users._partials.panels.user-details', ['user' => $user])
@stop

@section('toolbar')
	@include('administration.users._partials.toolbars.show')
@stop

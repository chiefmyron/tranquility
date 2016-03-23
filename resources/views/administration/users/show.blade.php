@extends('administration.app')

@section('menu')
	@include('administration._partials.menu', ['active' => 'users'])
@stop

@section('heading')
	@include('administration._partials.heading', ['heading' => trans('administration.users_heading_users_record', ['name' => $user->getDisplayName()])])
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.settings.users.show', $user))

@section('content')
    @include('administration.users._partials.panels.user-details', ['user' => $user])
@stop

@section('toolbar')
	@include('administration.users._partials.toolbars.show')
@stop

@extends('administration.app')

@section('menu')
	@include('administration._partials.menu', ['active' => 'users'])
@stop

@section('heading')
	@include('administration._partials.heading', ['heading' => trans('administration.users_command_update_user')])
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.settings.users.update', $user))

@section('content')
	{!! Form::model($user, ['action' => 'Administration\UsersController@storePersonUser']) !!}
    @include('administration.users._partials.forms.update')
    {!! Form::hidden('id', $user->id) !!}
    <div class="form-group">
        {!! Form::submit(trans('administration.users_command_update_user'), ['class' => 'btn btn-primary pull-right']) !!}
        <a href="{{ action('Administration\UsersController@showPersonUser', ['id' => $user->id]) }}" class="btn btn-default pull-left">{{ trans('administration.common_cancel') }}</a>
    </div>
	{!! Form::close() !!}
@stop
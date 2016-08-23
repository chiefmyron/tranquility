@extends('administration.app')

@section('menu')
	@include('administration._partials.menu', ['active' => 'accounts'])
@stop

@section('heading')
	@include('administration._partials.heading', ['heading' => trans('administration.accounts_heading_create')])
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.accounts.create'))

@section('content')
	{!! Form::open(['action' => 'Administration\AccountsController@store']) !!}
	@include('administration.accounts._partials.forms.create')
    <div class="form-group">
        {!! Form::submit(trans('administration.accounts_command_create'), ['class' => 'btn btn-primary pull-right']) !!}
        <a href="{{ action('Administration\AccountsController@index') }}" class="btn btn-default pull-left">{{ trans('administration.common_cancel') }}</a>
    </div>
	{!! Form::close() !!}
@stop
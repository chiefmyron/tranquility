@extends('administration.app')

@section('menu')
	@include('administration._partials.menu', ['active' => 'accounts'])
@stop

@section('heading')
	@include('administration._partials.heading', ['heading' => trans('administration.accounts_heading_update', ['name' => $account->name])])
@stop

@section('breadcrumbs')
	{!! Breadcrumbs::render('admin.accounts.update', $account) !!}
@stop

@section('content')
	{!! Form::model($account, ['action' => 'Administration\AccountsController@store']) !!}
    @include('administration.accounts._partials.forms.create')
    {!! Form::hidden('id', $account->id) !!}
    <div class="form-group">
        {!! Form::submit(trans('administration.accounts_command_update'), ['class' => 'btn btn-primary pull-right']) !!}
        <a href="{{ action('Administration\AccountsController@show', ['id' => $account->id]) }}" class="btn btn-default pull-left">{{ trans('administration.common_cancel') }}</a>
    </div>
	{!! Form::close() !!}
@stop
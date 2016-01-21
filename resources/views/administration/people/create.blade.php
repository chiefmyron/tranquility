@extends('administration.app')

@section('heading')
	@include('administration._partials.heading', ['heading' => trans('administration.people_heading_create')])
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.people.create'))

@section('sidebar')
	@include('administration._partials.sidebar', ['active' => 'people'])
@stop

@section('content')
	{!! Form::open(['action' => 'Administration\PeopleController@store']) !!}
	@include('administration.people._partials.forms.create')
    <div class="form-group">
        <a href="{{ action('Administration\PeopleController@index') }}" class="btn btn-default pull-left">{{ trans('administration.common_cancel') }}</a>
    	{!! Form::submit(trans('administration.people_command_create'), ['class' => 'btn btn-primary pull-right']) !!}
    </div>
	{!! Form::close() !!}
@stop
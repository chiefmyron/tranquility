@extends('administration.app')

@section('menu')
	@include('administration._partials.menu', ['active' => 'people'])
@stop

@section('heading')
	@include('administration._partials.heading', ['heading' => trans('administration.people_heading_create')])
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.people.create'))

@section('content')
	{!! Form::open(['action' => 'Administration\PeopleController@store']) !!}
	@include('administration.people._partials.forms.create')
    <div class="form-group">
        {!! Form::submit(trans('administration.people_command_create'), ['class' => 'btn btn-primary pull-right']) !!}
        <a href="{{ action('Administration\PeopleController@index') }}" class="btn btn-default pull-left">{{ trans('administration.common_cancel') }}</a>
    </div>
	{!! Form::close() !!}
@stop
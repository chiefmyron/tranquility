@extends('administration.app')

@section('menu')
	@include('administration._partials.menu', ['active' => 'people'])
@stop

@section('heading')
	@include('administration._partials.heading', ['heading' => trans('administration.people_heading_update', ['name' => $person->getFullName()])])
@stop

@section('breadcrumbs')
	{!! Breadcrumbs::render('admin.people.update', $person) !!}
@stop

@section('content')
	{!! Form::model($person, ['action' => 'Administration\PeopleController@store']) !!}
    @include('administration.people._partials.forms.create')
    {!! Form::hidden('id', $person->id) !!}
    <div class="form-group">
        {!! Form::submit(trans('administration.people_command_update'), ['class' => 'btn btn-primary pull-right']) !!}
        <a href="{{ action('Administration\PeopleController@show', ['id' => $person->id]) }}" class="btn btn-default pull-left">{{ trans('administration.common_cancel') }}</a>
    </div>
	{!! Form::close() !!}
@stop
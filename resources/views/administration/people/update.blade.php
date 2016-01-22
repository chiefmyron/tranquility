@extends('administration.app')

@section('heading')
	@include('administration._partials.heading', ['heading' => trans('administration.people_heading_update', ['name' => $person->getName()])])
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.people.update', $person))

@section('sidebar')
	@include('administration._partials.sidebar', ['active' => 'people'])
@stop

@section('content')
	{!! Form::model($person, ['action' => 'Administration\PeopleController@store']) !!}
    @include('administration.people._partials.forms.create')
    {!! Form::hidden('id', $person->id) !!}
    <div class="form-group">
        <a href="{{ action('Administration\PeopleController@show', ['id' => $person->id]) }}" class="btn btn-default pull-left">{{ trans('administration.common_cancel') }}</a>
    	{!! Form::submit(trans('administration.people_command_update'), ['class' => 'btn btn-primary pull-right']) !!}
    </div>
	{!! Form::close() !!}
@stop
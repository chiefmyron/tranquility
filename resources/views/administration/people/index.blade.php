@extends('administration.app')

@section('breadcrumbs', Breadcrumbs::render('admin.people'))

@section('content')
	<h1>{{ trans('administration.people_heading_people') }}</h1>
	<hr />
	@include('administration.errors.list')
	
	@foreach ($content as $person)
		<h2><a href="{{ action('Administration\PeopleController@show', [$person->id]) }}">{{ $person->firstName.' '.$person->lastName}}</a></h2>
		<div class="body">{{$person->position}}</div>
	@endforeach
@stop

@section('toolbar')
	{{ Toolbar::addHeading('Actions') }}	
	{{ Toolbar::addLink('Create new person', 'toolbar-add-new-person', action('Administration\PeopleController@create'), null, 'plus') }}
	{{ Toolbar::addLink('Delete selected people', 'toolbar-delete-multiple-people', action('Administration\PeopleController@index'), null, 'trash', false) }}
	{{ Toolbar::addDivider() }}
	{{ Toolbar::addLink('Logout selected users', 'toolbar-logout-multiple-people', action('Administration\PeopleController@index'), null, 'log-out', false) }}
	{{ Toolbar::addLink('Activate selected users', 'toolbar-activate-multiple-people', action('Administration\PeopleController@index'), null, 'ok', false) }}
	{{ Toolbar::addLink('Deactivate selected users', 'toolbar-deactivate-multiple-people', action('Administration\PeopleController@index'), null, 'remove', false) }}
	{!! Toolbar::render() !!}
@stop

		

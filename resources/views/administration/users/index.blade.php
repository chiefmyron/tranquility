@extends('administration.app')

@section('menu')
	@include('administration._partials.menu', ['active' => 'users'])
@stop

@section('heading')
	@include('administration._partials.heading', ['heading' => trans('administration.users_heading_users_people')])
@stop

@section('breadcrumbs', Breadcrumbs::render('admin.settings.users'))

@section('content')
        <div class="table-responsive">
			<table class="table table-striped data-table">
				<tr>
					<th class="checkboxcol">
						<input type="checkbox" name="selectAll" id="selectAll" value="" class="selectAll checkbox" />
					</th>
					<th>
						{{ trans('administration.people_name') }}	
					</th>
					<th>
						{{ trans('administration.common_username') }}
					</th>
					<th>
						{{ trans('administration.users_label_is_active') }}
					</th>
					<th>
						{{ trans('administration.users_label_is_logged_in') }}
					</th>
                    <th>
						{{ trans('administration.users_label_registered_date') }}
					</th>
				</tr>
		@foreach ($content as $user)
				<tr>
					<td class="checkboxcol">
						<input type="checkbox" name="id" value="{{ $user->id }}" class="record-select checkbox" />
					</td>
					<td>
						<a href="{{ action('Administration\UsersController@show', [$user->id]) }}">{{ $user->getDisplayName() }}</a>
					</td>
					<td>
						{{ $user->username }}
					</td>
					<td>
						{{ $user->active }}
					</td>
					<td>
						
					</td>
                    <td>
						{!! DateTimeFormatter::shortDateTime($user->registeredDateTime, true) !!}
					</td>
				</tr>
		@endforeach
				<tr>
					<td class="footer" colspan="6"></td>
				</tr>
			</table>
		</div>
@stop

@section('toolbar')
	@include('administration.users._partials.toolbars.index')
@stop
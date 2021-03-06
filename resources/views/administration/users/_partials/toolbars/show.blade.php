	{{ Toolbar::addHeading('administration.common_actions') }}
	{{ Toolbar::addLink(trans('administration.users_command_update_user'), 'toolbar-update-record', action('Administration\UsersController@update', ["id" => $user->id]), null, 'pencil') }}
	{{ Toolbar::addLink(trans('administration.users_command_change_password'), 'toolbar-change-password', action('Administration\UsersController@changePassword', ['id' => $user->id]), 'ajax', ' fa fa-key') }}
	{{ Toolbar::addDivider() }}
    {{ Toolbar::addLink('administration.users_command_delete_single_user', 'toolbar-delete-record', action('Administration\UsersController@confirm', ['id' => $user->id, 'action' => 'delete']), 'ajax', 'trash', !$currentUser) }}
	
    {{-- Only show link to activate user if currently suspended, and vice versa --}}
    @if($user->active)
	{{ Toolbar::addLink('administration.users_command_deactivate_single_user', 'toolbar-deactivate-user-account',  action('Administration\UsersController@confirm', ['id' => $user->id, 'action' => 'deactivate']), 'ajax', 'remove', !$currentUser) }}
    @else
    {{ Toolbar::addLink('administration.users_command_activate_single_user', 'toolbar-activate-user-account',  action('Administration\UsersController@confirm', ['id' => $user->id, 'action' => 'activate']), 'ajax multi-select', 'ok', false) }}
    @endif
    {{ Toolbar::addDivider() }}
	{{ Toolbar::addLink(trans('administration.users_command_back_to_users_list'), 'toolbar-back', action('Administration\UsersController@index'), null, 'chevron-left') }}
	{!! Toolbar::render() !!}
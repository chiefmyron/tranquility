	{{ Toolbar::addHeading('administration.common_actions') }}
	{{ Toolbar::addLink('administration.users_command_delete_selected_users', 'toolbar-delete-multiple-people', action('Administration\PeopleController@confirmAction', ['action' => 'delete']), 'ajax multi-select', 'trash', false) }}
	{{ Toolbar::addLink('administration.users_command_logout_selected_users', 'toolbar-logout-multiple-people', action('Administration\PeopleController@index'), 'ajax multi-select', 'log-out', false) }}
	{{ Toolbar::addLink('administration.users_command_activate_selected_users', 'toolbar-activate-multiple-people', action('Administration\PeopleController@index'), 'ajax multi-select', 'ok', false) }}
	{{ Toolbar::addLink('administration.users_command_deactivate_selected_users', 'toolbar-deactivate-multiple-people', action('Administration\PeopleController@index'), 'ajax multi-select', 'remove', false) }}
    {{ Toolbar::addHeading('administration.common_tips') }}
    {{ Toolbar::addText('administration.users_tip_text_index') }}
	{!! Toolbar::render() !!}
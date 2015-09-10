	{{ Toolbar::addHeading('Actions') }}	
	{{ Toolbar::addLink('Create new person', 'toolbar-add-new-person', action('Administration\PeopleController@create'), null, 'plus') }}
	{{ Toolbar::addLink('Delete selected people', 'toolbar-delete-multiple-people', action('Administration\PeopleController@index'), null, 'trash', false) }}
	{{ Toolbar::addDivider() }}
	{{ Toolbar::addLink('Switch to detail view', 'toolbar-logout-multiple-people', action('Administration\PeopleController@index'), null, 'user', false) }}
	{{ Toolbar::addDivider() }}
	{{ Toolbar::addLink('Logout selected users', 'toolbar-logout-multiple-people', action('Administration\PeopleController@index'), null, 'log-out', false) }}
	{{ Toolbar::addLink('Activate selected users', 'toolbar-activate-multiple-people', action('Administration\PeopleController@index'), null, 'ok', false) }}
	{{ Toolbar::addLink('Deactivate selected users', 'toolbar-deactivate-multiple-people', action('Administration\PeopleController@index'), null, 'remove', false) }}
	{!! Toolbar::render() !!}
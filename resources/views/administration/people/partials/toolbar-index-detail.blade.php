	{{ Toolbar::addHeading('Actions') }}	
	{{ Toolbar::addLink('Create new person', 'toolbar-add-new-person', action('Administration\PeopleController@create'), null, 'plus') }}
	{{ Toolbar::addDivider() }}
	{{ Toolbar::addLink('Switch to list view', 'toolbar-logout-multiple-people', action('Administration\PeopleController@index'), null, 'th-list', false) }}
	{!! Toolbar::render() !!}
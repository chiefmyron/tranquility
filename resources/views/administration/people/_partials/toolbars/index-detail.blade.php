	{{ Toolbar::addHeading('Actions') }}	
	{{ Toolbar::addLink('Create new person', 'toolbar-add-new-person', action('Administration\PeopleController@create'), null, 'plus') }}
	{{ Toolbar::addDivider() }}
	{{ Toolbar::addLink('Switch to list view', 'toolbar-switch-view-list', action('Administration\PeopleController@index', ['view' => 'list']), 'ajax', 'th-list') }}
	{!! Toolbar::render() !!}
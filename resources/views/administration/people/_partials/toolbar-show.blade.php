	{{ Toolbar::addHeading('Actions') }}
	{{ Toolbar::addLink('Delete record for this person', 'toolbar-delete-record', action('Administration\PeopleController@index'), 'ajax', 'trash') }}
	{{ Toolbar::addDivider() }}
	{{ Toolbar::addLink('Back to list of people', 'toolbar-back', action('Administration\PeopleController@index'), null, 'chevron-left') }}
	{!! Toolbar::render() !!}
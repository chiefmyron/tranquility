	{{ Toolbar::addHeading('Actions') }}
	{{ Toolbar::addLink('Update person details', 'toolbar-update-record', action('Administration\PeopleController@update', ["id" => $person->id]), null, 'pencil') }}
	{{ Toolbar::addLink('Delete record for this person', 'toolbar-delete-record', action('Administration\PeopleController@confirmAction', ['id' => $person->id, 'action' => 'delete']), 'ajax', 'trash') }}
	{{ Toolbar::addDivider() }}
	{{ Toolbar::addLink('Back to list of people', 'toolbar-back', action('Administration\PeopleController@index'), null, 'chevron-left') }}
	{!! Toolbar::render() !!}
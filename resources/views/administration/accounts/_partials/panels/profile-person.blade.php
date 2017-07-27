<li id="profile-item-person">
    <div class="icon">
        <i class="icon-user"></i>
    </div>
    <div class="data-item">
        <span class="heading">{{ trans('administration.accounts_label_primary_contact') }}</span>
        @if($primaryContact == null)
            <span>{{ trans('administration.accounts_message_no_contacts') }}</span>
            <span class="action"><a href="{{ action('Administration\PeopleController@create') }}" class="ajax" data-ajax-preload-target="modal"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.people_command_add_contact') }}</a></span>
        @else
            <p><a href="{{ action('Administration\PeopleController@show', ['id' => $primaryContact->getPerson()->id]) }}">{{ $primaryContact->getPerson()->getFullName() }}</a></p>
        @endif
    </div>
</li>
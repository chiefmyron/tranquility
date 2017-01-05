<li id="profile-item-account">
    <div class="icon">
        <i class="icon-briefcase"></i>
    </div>
    <div class="data-item">
        <span class="heading">{{ trans('administration.people_label_company') }}</span>
@if (!is_null($account) && $person->position != '')
        <span>{{ $person->position }} at <a href="{{ action('Administration\AccountsController@show', ['id' => $account->id]) }}">{{ $account->name }}</a></span>
@elseif (!is_null($account) && $person->position == '')
        <span><a href="{{ action('Administration\AccountsController@show', ['id' => $account->id]) }}">{{ $account->name }}</a></span>
@else
        <span><em>{{ trans('administration.people_label_no_company') }}</em></span>
@endif
    </div>
</li>
<div class="search-results">
    <p class="record-count">{{ trans_choice('administration.search_message_num_results', $totalResults, ['total' => $totalResults]) }}</p>

    {{-- Results block for Person entities --}}
    @if (count($results['person']) > 0)
        <h3>{{ trans('administration.people_heading_people') }}</h3>
        @foreach ($results['person'] as $person)
            <div class="search-result-item entity-person">
                <p class="primary"><a href="{{ action('Administration\PeopleController@show', [$person->id]) }}">{{ $person->getFullName() }}</a></p>
                <p class="secondary">{{ $person->position }}</p>
                <p class="tags">@include('administration.tags._partials.panels.entity-tag-list', ['entity' => $person, 'tags' => $person->getTags(), 'editable' => false])</p>
            </div>
        @endforeach
    @endif

    {{-- Results block for Account entities --}}
    @if (count($results['account']) > 0)
    <h3>{{ trans('administration.accounts_heading_accounts') }}</h3>
        @foreach ($results['account'] as $account)
        <div class="search-result-item entity-account">
            <p class="primary"><a href="{{ action('Administration\AccountsController@show', [$account->id]) }}">{{ $account->name }}</a></p>
            <p class="tags">@include('administration.tags._partials.panels.entity-tag-list', ['entity' => $account, 'tags' => $account->getTags(), 'editable' => false])</p>
        </div>
        @endforeach
    @endif

</div>



<div class="row">
    <div class="col-xs-12">
        {{-- Results block for Person entities --}}
        @if (count($results['person']) > 0)
        <h3>{{ trans('administration.people_heading_people') }}</h3>
            @foreach ($results['person'] as $person)
                <p><a href="{{ action('Administration\PeopleController@show', [$person->id]) }}">{{ $person->getFullName() }}</a></p>
            @endforeach
        @endif

        {{-- Results block for Account entities --}}
        @if (count($results['account']) > 0)
        <h3>{{ trans('administration.accounts_heading_accounts') }}</h3>
            @foreach ($results['account'] as $account)
                <p><a href="{{ action('Administration\AccountsController@show', [$account->id]) }}">{{ $account->name }}</a></p>
            @endforeach
        @endif
    </div>
</div>
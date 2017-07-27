{{-- Sidebar for Account details page --}}
<div id="account-details-container">
    <div class="entity-profile">
        <ul>
            {{-- Primary contact section --}}
            @include('administration.accounts._partials.panels.profile-person', ['primaryContact' => $account->getPrimaryContact()])

            {{-- Primary address / quick contact section --}}
            @include('administration.addresses._partials.panels.profile-primary-address', ['entity' => $account])
            
            {{-- Tags section --}}
            @include('administration.tags._partials.panels.profile-tag-list', ['entity' => $account])
        </ul>    
    </div>
</div>
{{-- Sidebar for Person details page --}}
<div id="person-details-container">
    <div class="profile-picture-container">
        <a href="#"><img class="profile-picture" src="/backend/images/user-avatar-default.png" alt="..."></a>
    </div>
    
    <div class="entity-profile">
        <ul>
            {{-- Company / job description --}}
            @include('administration.people._partials.panels.profile-account', ['account' => $account, 'person' => $person])

            {{-- User account --}}
            @include('administration.users._partials.panels.profile-user', ['user' => $user])

            {{-- Primary address / quick contact section --}}
            @include('administration.addresses._partials.panels.profile-primary-address', ['entity' => $person])

            {{-- Tags section --}}
            @include('administration.tags._partials.panels.profile-tag-list', ['entity' => $person])
        </ul>    
    </div>
</div>
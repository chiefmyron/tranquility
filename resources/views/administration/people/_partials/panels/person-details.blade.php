            <div id="person-details-container">
                <div class="profile-picture-container">
                    <a href="#"><img class="profile-picture" src="/backend/images/user-avatar-default.png" alt="..."></a>
                </div>
                
                <div class="entity-profile">
                    <ul>
                        <li>
                            <div class="icon">
                                <i class="icon-briefcase"></i>
                            </div>
                            <div class="data-item">
                                <span class="heading">{{ trans('administration.people_label_company') }}</span>
                        @if(is_null($account))        
                                <span>No company</span>
                        @else
                                <span>{{ $person->position }} at <a href="{{ action('Administration\AccountsController@show', ['id' => $account->id]) }}">{{ $account->name }}</a></span>
                        @endif
                            </div>
                        </li>

                        <li>
                            <div class="icon">
                                <i class="icon-user"></i>
                            </div>
                            <div class="data-item">
                                <span class="heading">{{ trans('administration.people_label_user_account') }}</span>
                        @if(is_null($user))                            
                                <span>{{ trans('administration.people_label_no_user_account') }}</span>
                                <span class="action"><a href="{{ action('Administration\PeopleController@createUser', ['id' => $person->id]) }}" class="ajax" data-ajax-preload-target="modal"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.users_heading_create_user') }}</a></span>
                            </div>
                        </li>
                        @else
                                <span><a href="{{ action('Administration\UsersController@show', ['id' => $user->id]) }}">{{ $user->username }}</a></span><br />
                            @if($user->active)
                                <span><strong class="text-success">{{ trans('administration.users_status_active') }}</strong></span>
                            @else
                                <span><strong class="text-warning">{{ trans('administration.users_status_suspended') }}</strong></span>
                            @endif  
                            </div>
                        </li>
                        @endif

                        {{-- Primary address / quick contact section --}}
                        @include('administration.addresses._partials.panels.profile-primary-address', ['entity' => $person])

                        {{-- Tags section --}}
                        @include('administration.tags._partials.panels.profile-tag-list', ['entity' => $person])
                    </ul>    
                </div>
            </div>
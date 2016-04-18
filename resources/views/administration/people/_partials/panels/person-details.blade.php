            <div id="person-details-container">
                <div class="profile-picture-container">
                    <a href="#"><img class="profile-picture" src="/backend/images/user-avatar-default.png" alt="..."></a>
                </div>
                
                <div class="entity-profile">
                    <ul>
                        <li>
                            <div class="icon">
                                <i class="icon-tag"></i>
                            </div>
                            <div class="data-item">
                                <span class="heading">{{ trans('administration.common_tags') }}</span>
                                <span>@include('administration.tags._partials.panels.entity-tag-list', ['entity' => $person, 'tags' => $person->getTags()])</span>
                                <span class="action"><a href="{{ action('Administration\TagsController@update', ['parentId' => $person->id]) }}" class="ajax"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.tags_command_add_tag') }}</a></span>
                            </div>
                        </li>
                        <li>
                            <div class="icon">
                                <i class="icon-briefcase"></i>
                            </div>
                            <div class="data-item">
                                <span class="heading">Company</span>
                                <span>None</span>
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
                                <span class="action"><a href="{{ action('Administration\PeopleController@createUser', ['id' => $person->id]) }}" class="ajax"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.users_heading_create_user') }}</a></span>
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
                        
                        @include('administration.addresses._partials.panels.profile-physical-address', ['addresses' => $person->getAddresses('physical'), 'parentId' => $person->id])
                        @include('administration.addresses._partials.panels.profile-email-address', ['addresses' => $person->getAddresses('email'), 'parentId' => $person->id])
                        @include('administration.addresses._partials.panels.profile-phone-address', ['addresses' => $person->getAddresses('phone'), 'parentId' => $person->id])
                        
                    </ul>    
                </div>
            </div>
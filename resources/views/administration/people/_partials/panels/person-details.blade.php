        <div id="person-details-container">
            <div class="row">
                <div class="col-sm-2 profile-picture-container">
                    <a href="#"><img class="profile-picture" src="/backend/images/user-avatar-default.png" alt="..."></a>
                </div>
                
                <div class="col-sm-5">
                    <dl class="data-list">
                        <dt>{{ trans('administration.people_label_title') }}</dt>
                        <dd>{{ $person->title or "&nbsp;" }}</dd>
                        
                        <dt>{{ trans('administration.people_label_first_name') }}</dt>
                        <dd>{{ $person->firstName }}</dd>
                        
                        <dt>{{ trans('administration.people_label_last_name') }}</dt>
                        <dd>{{ $person->lastName }}</dd>
                    </dl>
                </div>
                
                <div class="col-sm-5">
                    <dl class="data-list">
                        <dt>{{ trans('administration.people_label_user_account') }}</dt>
                        @if(is_null($user))
                        <dd>
                            {{ trans('administration.people_label_no_user_account') }}
                            <p><a href="{{ action('Administration\PeopleController@createUser', ['id' => $person->id]) }}" class="ajax"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.users_heading_create_user') }}</a></p>
                        </dd>
                        @else
                        <dd><a href="{{ action('Administration\UsersController@show', ['id' => $user->id]) }}">{{ $user->username }}</a></dd>
                        
                        <dt>{{ trans('administration.users_label_account_status') }}</dt>
                            @if($user->active)
                        <dd><strong class="text-success">{{ trans('administration.users_status_active') }}</strong></dd>    
                            @else
                        <dd><strong class="text-warning">{{ trans('administration.users_status_suspended') }}</strong></dd>
                            @endif
                        @endif
                        
                        <dt>{{ trans('administration.common_tags') }}</dt>
                        <dd>
                            @include('administration.tags._partials.panels.entity-tag-list', ['entity' => $person, 'tags' => $person->getTags()])
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
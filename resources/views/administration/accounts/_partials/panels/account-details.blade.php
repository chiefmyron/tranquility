            <div id="person-details-container">
                <div class="profile-picture-container">
                    <a href="#"><img class="profile-picture" src="/backend/images/user-avatar-default.png" alt="..."></a>
                </div>
                
                <div class="entity-profile">
                    <ul>
                        <li>
                            <div class="icon">
                                <i class="icon-user"></i>
                            </div>
                            <div class="data-item">
                                <span class="heading">{{ trans('administration.people_label_contacts') }}</span>
                        @if(count($account->getContacts()) <= 0)     
                                               
                                <span>{{ trans('administration.account_label_no_contacts') }}</span>
                                <span class="action"><a href="{{ action('Administration\PeopleController@createUser', ['id' => $account->id]) }}" class="ajax" data-ajax-preload-target="modal"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.account_label_add_contact') }}</a></span>
                            </div>
                        </li>
                        @else
                                <span><a href="{{ action('Administration\PeopleController@show', ['id' => $account->getPrimaryContact()->id]) }}">{{ $account->getPrimaryContact()->getFullName() }}</a></span><br />
                            </div>
                        </li>
                        @endif

                        <?php $primaryAddresses = $account->getPrimaryAddresses(); ?>
                        @if (count($primaryAddresses) > 0)
                        <li>
                            <div class="icon">
                                <i class="icon-envelope"></i>
                            </div>
                            <div class="data-item">
                                <span class="heading">{{ trans('administration.common_quick_contact') }}</span>
                                @if (isset($primaryAddresses['email']))
                                <p>
                                    <a href="mailto:{{ $primaryAddresses['email']->addressText }}">{{ $primaryAddresses['email']->addressText }}</a>
                                </p>
                                @endif
                                @if (isset($primaryAddresses['phone']))
                                <p class="h-adr">
                                    <a href="tel:{{ $primaryAddresses['phone']->addressText }}">{{ $primaryAddresses['phone']->addressText }}</a>
                                </p>
                                @endif
                            </div>
                        </li>
                        @endif
                        
                        <li>
                            <div class="icon">
                                <i class="icon-tag"></i>
                            </div>
                            <div class="data-item">
                                <span class="heading">{{ trans('administration.common_tags') }}</span>
                                <span>@include('administration.tags._partials.panels.entity-tag-list', ['entity' => $account, 'tags' => $account->getTags()])</span>
                                <span class="action"><a href="{{ action('Administration\TagsController@update', ['parentId' => $account->id]) }}" class="ajax" ><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.tags_command_add_tag') }}</a></span>
                            </div>
                        </li>
                    </ul>    
                </div>
            </div>
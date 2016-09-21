<?php
// Setup data for view
$contacts = $account->getContacts();
$primaryContact = $account->getPrimaryContact();

$primaryContactAddresses = array();
if ($primaryContact !== null) {
    $primaryContactAddresses = $primaryContact->getPrimaryAddresses();
}
?>

            <div id="person-details-container">
                <div class="profile-picture-container">
                    <a href="#"><img class="profile-picture" src="/backend/images/user-avatar-default.png" alt="..."></a>
                </div>
                
                <div class="entity-profile">
                    <ul>
                        {{-- Primary contact section --}}
                        @if($primaryContact == null)
                        <li>
                            <div class="icon">
                                <i class="icon-user"></i>
                            </div>
                            <div class="data-item">
                                <span class="heading">{{ trans('administration.accounts_label_primary_contact') }}</span>
                                <span>{{ trans('administration.account_label_no_contacts') }}</span>
                                <span class="action"><a href="{{ action('Administration\PeopleController@create') }}" class="ajax" data-ajax-preload-target="modal"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.account_label_add_contact') }}</a></span>
                            </div>
                        </li>
                        @else
                        <li>
                            <div class="icon">
                                <i class="icon-user"></i>
                            </div>
                            <div class="data-item">
                                <span class="heading">{{ trans('administration.accounts_label_primary_contact') }}</span>
                                <p>{{ $primaryContact->getFullName() }}</p>
                                @if (isset($primaryContactAddresses['email']))
                                <p>
                                    <a href="mailto:{{ $primaryContactAddresses['email']->addressText }}">{{ $primaryContactAddresses['email']->addressText }}</a>
                                </p>
                                @endif
                                @if (isset($primaryContact->getPrimaryAddresses()['phone']))
                                <p class="h-adr">
                                    <a href="tel:{{ $primaryContactAddresses['phone']->addressText }}">{{ $primaryContactAddresses['phone']->addressText }}</a>
                                </p>
                                @endif
                            </div>
                        </li>
                        @endif

                        {{-- Primary address / quick contact section --}}
                        @include('administration.addresses._partials.panels.profile-primary-address', ['entity' => $account])
                        
                        {{-- Tags section --}}
                        @include('administration.tags._partials.panels.profile-tag-list', ['entity' => $account])
                    </ul>    
                </div>
            </div>
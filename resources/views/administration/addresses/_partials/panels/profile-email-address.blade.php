                        <li id="email-addresses-container">
                            <div class="icon">
                                <i class="icon-envelope"></i>
                            </div>
                            <div class="data-item">
                                <span class="heading">{{ trans('administration.address_heading_email_addresses') }}</span>
@if (count($addresses) <= 0)
                                <div class="element">
                                    <span>None</span>
                                    <span class="action">
                                        <a href="{{ action('Administration\AddressController@create', ['type' => 'email', 'parentId' => $parentId]) }}" class="ajax"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.address_heading_add_new_email') }}</a>
                                    </span>
                                </div>
@else
    @foreach ($addresses as $address)
                                <div class="element">
                                    <div class="dropdown">
                                        <button type="button" class="action-menu-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="icon-arrow-down"></i><span class="sr-only">Actions</span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a href="{{ action('Administration\AddressController@confirm', ['id' => $address->id, 'type' => 'email']) }}" class="ajax">{{ trans('administration.common_delete') }}</a></li>
                                            <li><a href="{{ action('Administration\AddressController@update', ['id' => $address->id, 'type' => 'email']) }}" class="ajax">{{ trans('administration.common_update') }}</a></li>
                                            @if (!$address->primaryContact)
                                            <li><a href="{{ action('Administration\AddressController@makePrimary', ['id' => $address->id, 'type' => 'email']) }}" class="ajax">{{ trans('administration.address_command_make_primary') }}</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                    <div>
                                        <span><a href="mailto:{{ $address->addressText }}">{{ $address->addressText }}</a></span>
                                        <span class="sub">{{ trans('administration.address_label_'.$address->addressType.'_email') }}</span>
                                    </div>
                                </div>
                                <hr />
    @endforeach
                                <span class="action">
                                    <a href="{{ action('Administration\AddressController@create', ['type' => 'email', 'parentId' => $parentId]) }}" class="ajax">
                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.address_heading_add_new_email') }}
                                    </a>     
                                </span>
@endif
                            </div>
                        </li>
                        <li id="email-addresses-container">
                            <div class="icon">
                                <i class="icon-envelope"></i>
                            </div>
                            <div class="data-item">
                                <span class="heading">{{ trans('administration.address_heading_email_addresses') }}</span>
@if (count($addresses) <= 0)
                                <span>None</span>
                                <span class="action">
                                    <a href="{{ action('Administration\AddressController@create', ['type' => 'email', 'parentId' => $parentId]) }}" class="ajax"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.address_heading_add_new_email') }}</a>
                                </span>
@else
    @foreach ($addresses as $address)
                                <span><a href="mailto:{{ $address->addressText }}">{{ $address->addressText }}</a></span>
                                <span class="sub">{{ trans('administration.address_label_'.$address->addressType.'_email') }}</span>
                                <span class="action">
                                   <a href="{{ action('Administration\AddressController@confirm', ['id' => $address->id, 'type' => 'email']) }}" class="ajax">{{ trans('administration.common_delete') }}</a>
                                 | <a href="{{ action('Administration\AddressController@update', ['id' => $address->id, 'type' => 'email']) }}" class="ajax">{{ trans('administration.common_update') }}</a>
                                @if (!$address->primaryContact)
                                 | <a href="{{ action('Administration\AddressController@makePrimary', ['id' => $address->id, 'type' => 'email']) }}" class="ajax">{{ trans('administration.address_command_make_primary') }}</a>
                                @endif
                                </span>
                                <br />
    @endforeach
                                <span class="action">
                                    <a href="{{ action('Administration\AddressController@create', ['type' => 'email', 'parentId' => $parentId]) }}" class="ajax">
                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.address_heading_add_new_email') }}
                                    </a>     
                                </span>
@endif
                            </div>
                        </li>
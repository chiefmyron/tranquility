                        <li id="phone-addresses-container">
                            <div class="icon">
                                <i class="icon-phone"></i>
                            </div>
                            <div class="data-item">
                                <span class="heading">{{ trans('administration.address_heading_phone_addresses') }}</span>
@if (count($addresses) <= 0)
                                <span>None</span>
                                <span class="action">
                                    <a href="{{ action('Administration\AddressController@create', ['type' => 'phone', 'parentId' => $parentId]) }}" class="ajax"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.address_heading_add_new_phone') }}</a>
                                </span>
@else
    @foreach ($addresses as $address)
                                <p class="h-adr">
                                    <a href="tel:{{ $address->addressText }}">{{ $address->addressText }}</a>
                                    <span class="sub">{{ trans('administration.address_label_'.$address->addressType.'_phone') }}</span>
                                </p>
                                <span class="action">
                                   <a href="{{ action('Administration\AddressController@confirm', ['id' => $address->id, 'type' => 'phone']) }}" class="ajax">{{ trans('administration.common_delete') }}</a>
                                 | <a href="{{ action('Administration\AddressController@update', ['id' => $address->id, 'type' => 'phone']) }}" class="ajax">{{ trans('administration.common_update') }}</a>
                                @if (!$address->primaryContact)
                                 | <a href="{{ action('Administration\AddressController@makePrimary', ['id' => $address->id, 'type' => 'phone']) }}" class="ajax">{{ trans('administration.address_command_make_primary') }}</a>
                                @endif
                                </span>
                                <br />
    @endforeach
                                <span class="action">
                                    <a href="{{ action('Administration\AddressController@create', ['type' => 'phone', 'parentId' => $parentId]) }}" class="ajax">
                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.address_heading_add_new_phone') }}
                                    </a>     
                                </span>
@endif
                            </div>
                        </li>
                        <li id="physical-addresses-container">
                            <div class="icon">
                                <i class="icon-map"></i>
                            </div>
                            <div class="data-item">
                                <span class="heading">{{ trans('administration.address_heading_physical_addresses') }}</span>
@if (count($addresses) <= 0)
                                <span>None</span>
                                <span class="action"><a href="{{ action('Administration\AddressController@create', ['type' => 'physical', 'parentId' => $parentId]) }}" class="ajax"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.address_heading_add_new_address') }}</a></span>
@else
    @foreach ($addresses as $address)
                                <div class="dropdown">
                                    <button type="button" class="action-menu-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="icon-arrow-down"></i><span class="sr-only">Actions</span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="{{ action('Administration\AddressController@confirm', ['id' => $address->id, 'type' => 'physical']) }}" class="ajax">{{ trans('administration.common_delete') }}</a></li>
                                        <li><a href="{{ action('Administration\AddressController@update', ['id' => $address->id, 'type' => 'physical']) }}" class="ajax">{{ trans('administration.common_update') }}</a></li>
                                        <li><a href="{{ action('Administration\AddressController@displayMap', [$address->id]) }}" class="ajax">{{ trans('administration.address_command_view_map') }}</a></li>
                                    </ul>
                                </div>
                                <div>
                                    
                                    <p class="h-adr">
                                        <span class="p-street-address">{{ $address->addressLine1 }},</span>
                                        @if (isset($address->addressLine2) && $address->addressLine2 != '') 
                                        <span class="p-extended-address">{{ $address->addressLine2 }}</span>
                                        @endif
                                        @if (isset($address->addressLine3) && $address->addressLine3 != '') 
                                        , <span class="p-extended-address">{{ $address->addressLine3 }}</span>
                                        @endif
                                        @if (isset($address->addressLine4) && $address->addressLine4 != '') 
                                        , <span class="p-extended-address">{{ $address->addressLine4 }}</span>
                                        @endif
                                        <br /><span class="p-locality">{{ $address->city }}</span>
                                        <span class="p-region">{{ $address->state }}</span>
                                        <span class="p-postal-code">{{ $address->postcode }}</span>
                                        @if (isset($address->country) && $address->country != '') 
                                        <br /><span class="p-country-name">{{ trans('countries.'.$address->country) }}</span>
                                        @endif
                                        
                                        <span class="sub">{{ trans('administration.address_label_'.$address->addressType.'_address') }}</span>
                                    </p>
                                </div>

                                <hr />
    @endforeach
                                <span class="action">
                                    <a href="{{ action('Administration\AddressController@create', ['type' => 'physical', 'parentId' => $parentId]) }}" class="ajax">
                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.address_command_add_another_address') }}
                                    </a>    
                                </span>
@endif
                            </div>
                        </li>
        <div id="physical-addresses-container">
            <h3>{{ trans('administration.address_heading_physical_addresses') }}</h3>
@if (count($addresses) <= 0)
            <div class="row">
                <div class="col-md-12">
                <div class="data-item text-center">
                    <p class="large">{{ trans('administration.address_message_no_physical_addresses') }}</p>
                    <p class="large">
                        <a href="{{ action('Administration\AddressController@create', ['type' => 'physical', 'parentId' => $parentId]) }}" class="ajax" data-ajax-preload-target="modal">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.address_heading_add_new_address') }}
                        </a>
                    </p>
                </div>
                </div>
            </div>
@else 
    @for ($i = 0; $i < count($addresses); $i++)
        @if ($i%2 == 0)
            <div class="row">
        @endif
                <div class="col-lg-6 col-md-12">
                    <div class="data-item">
                        <div class="data-header">
                            <span class="heading">{{ trans('administration.address_label_'.$addresses[$i]->addressType.'_address') }}</span>
                            <span class="action">
                                <a href="{{ action('Administration\AddressController@confirm', ['id' => $addresses[$i]->id, 'type' => 'physical']) }}" class="ajax" data-ajax-preload-target="modal">{{ trans('administration.common_delete') }}</a> |
                                <a href="{{ action('Administration\AddressController@update', ['id' => $addresses[$i]->id, 'type' => 'physical']) }}" class="ajax" data-ajax-preload-target="modal">{{ trans('administration.common_update') }}</a> |
                                <a href="{{ action('Administration\AddressController@displayMap', [$addresses[$i]->id]) }}" class="ajax" data-ajax-preload-target="modal">{{ trans('administration.address_command_view_map') }}</a>    
                            </span>
                        </div>
                        <p class="h-adr">
                            <span class="p-street-address">{{ $addresses[$i]->addressLine1 }}</span>
                            @if (isset($addresses[$i]->addressLine2) && $addresses[$i]->addressLine2 != '') 
                            <span class="p-extended-address">{{ $addresses[$i]->addressLine2 }}</span>
                            @endif
                            @if (isset($addresses[$i]->addressLine3) && $addresses[$i]->addressLine3 != '') 
                            <span class="p-extended-address">{{ $addresses[$i]->addressLine3 }}</span>
                            @endif
                            @if (isset($addresses[$i]->addressLine4) && $addresses[$i]->addressLine4 != '') 
                            <span class="p-extended-address">{{ $addresses[$i]->addressLine4 }}</span>
                            @endif
                            <span class="p-locality">{{ $addresses[$i]->city }}</span>
                            <span class="p-region">{{ $addresses[$i]->state }}</span>
                            <span class="p-postal-code">{{ $addresses[$i]->postcode }}</span>
                            @if (isset($addresses[$i]->country) && $addresses[$i]->country != '') 
                            <span class="p-country-name">{{ trans('countries.'.$addresses[$i]->country) }}</span>
                            @endif
                        </p>
                        
                        
                    </div>
                </div>
        @if ($i%2 == 1)
            </div><!-- End of row -->
        @endif
    @endfor

    @if (count($addresses)%2 == 0)
            <div class="row">
    @endif
                <div class="col-md-6 text-center data-action">
                    <a href="{{ action('Administration\AddressController@create', ['type' => 'physical', 'parentId' => $parentId]) }}" class="ajax large" data-ajax-preload-target="modal">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.address_command_add_another_address') }}
                    </a>    
                </div>
            </div>
@endif
        </div>

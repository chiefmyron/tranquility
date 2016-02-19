@if (count($addresses) <= 0)
            <div class="row">
                <div class="col-md-12">
                <div class="data-item text-center">
                    <p class="large">{{ trans('administration.people_message_no_physical_addresses') }}</p>
                    <p class="large"><a href="{{ action('Administration\PeopleController@addPhysicalAddress', [$parentId]) }}" class="ajax"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.address_heading_add_new_address') }}</a></p>
                </div>
                </div>
            </div>
@else 
    @for ($i = 0; $i < count($addresses); $i++)
        @if ($i%3 == 0)
            <div class="row">
        @endif
                <div class="col-md-4">
                    <div class="data-item">
                        <div class="data-header">
                            <h4>{{ trans('administration.address_label_'.$addresses[$i]->addressType.'_address') }}</h4>
                            <span class="action">
                                <a href="{{ action('Administration\PeopleController@confirmAction', ['id' => $addresses[$i]->id, 'parentId' => $parentId, 'action' => 'deleteAddress']) }}" class="ajax">Delete</a> |
                                <a href="{{ action('Administration\PeopleController@updatePhysicalAddress', ['id' => $addresses[$i]->id, 'parentId' => $parentId]) }}" class="ajax">Update</a> |
                                <a href="{{ action('Administration\AddressController@displayMap', [$addresses[$i]->id]) }}" class="ajax">View map</a>    
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
        @if ($i%3 == 2)
            </div><!-- End of row -->
        @endif
    @endfor

    @if (count($addresses)%3 == 0)
            <div class="row">
    @endif
                <div class="col-md-4 text-center">
                    <div class="data-item">
                        <a href="{{ action('Administration\PeopleController@addPhysicalAddress', [$parentId]) }}" class="ajax large"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add another address</a>    
                    </div>
                </div>
            </div>
@endif

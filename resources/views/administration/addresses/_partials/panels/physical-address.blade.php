@for ($i = 0; $i < count($addresses); $i++)
    @if ($i%2 == 0)
        <div class="row">
    @endif
            <div class="col-md-6">
                <div class="data-item">
                    <div class="data-header">
                        <h4>{{ trans('administration.address_label_'.$addresses[$i]->addressType.'_address') }}</h4>
                        <span class="action">
                            <a href="#">Delete</a> |
                            <a href="#">Update</a> |
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
                        <span class="p-country-name">{{ $addresses[$i]->country }}</span>
                        @endif
                    </p>
                </div>
            </div>
    @if (($i%2 == 1) || ($i == count($addresses) - 1))
        </div><!-- End of row -->
    @endif
@endfor
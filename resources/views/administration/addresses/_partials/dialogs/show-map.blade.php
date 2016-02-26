<div class="modal-body full-bleed">
    <div class="row">
        <div class="col-md-8 full-bleed">
            <iframe width="100%" height="400" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.co.uk/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q={{ $address->urlEncodedAddress() }}&amp;aq=0&amp;t=m&amp;ie=UTF8&amp;hq=&amp;hnear={{ $address->urlEncodedAddress() }}&amp;z=14&amp;iwloc=&amp;output=embed&amp;ll={{ $address->latitude }},{{ $address->longitude }}"></iframe>        
        </div>
        <div class="col-md-4 content">
            <h3 class="modal-title" id="modal-dialog-title">{{ trans('administration.address_label_'.$address->addressType.'_address') }}</h3>
            <hr />
            <p class="h-adr">
                <span class="p-street-address">{{ $address->addressLine1 }}</span>
                @if (isset($address->addressLine2) && $address->addressLine2 != '') 
                <span class="p-extended-address">{{ $address->addressLine2 }}</span>
                @endif
                @if (isset($address->addressLine3) && $address->addressLine3 != '') 
                <span class="p-extended-address">{{ $address->addressLine3 }}</span>
                @endif
                @if (isset($address->addressLine4) && $address->addressLine4 != '') 
                <span class="p-extended-address">{{ $address->addressLine4 }}</span>
                @endif
                <span class="p-locality">{{ $address->city }}</span>
                <span class="p-region">{{ $address->state }}</span>
                <span class="p-postal-code">{{ $address->postcode }}</span>
                @if (isset($address->country) && $address->country != '') 
                <span class="p-country-name">{{ trans('countries.'.$address->country) }}</span>
                @endif
            </p>
            <button type="button" class="btn btn-primary" data-dismiss="modal">{{ trans('administration.common_go_back') }}</button>
        </div>
    </div>
</div>
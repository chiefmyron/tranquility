<div class="modal-header">
    <button class="close" data-dismiss="modal" aria-label="{{ trans('administration.common_cancel') }}"><span aria-hidden="true">&times;</span></button>
    <h3 class="modal-title" id="modal-dialog-title">{{ trans('administration.address_label_'.$address->addressType.'_address') }}</h3>
</div>
<div class="modal-body">
    <iframe width="100%" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.co.uk/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q={{ $address->urlEncodedAddress() }}&amp;aq=0&amp;t=m&amp;ie=UTF8&amp;hq=&amp;hnear={{ $address->urlEncodedAddress() }}&amp;z=14&amp;iwloc=&amp;output=embed&amp;ll={{ $address->latitude }},{{ $address->longitude }}"></iframe>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-primary" data-dismiss="modal">{{ trans('administration.common_go_back') }}</button>
</div>
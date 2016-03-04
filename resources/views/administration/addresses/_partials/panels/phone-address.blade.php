        <div id="phone-addresses-container">
            <h3>{{ trans('administration.address_heading_phone_addresses') }}</h3>
@if (count($addresses) <= 0)
            <div class="row">
                <div class="col-md-12">
                <div class="data-item text-center">
                    <p class="large">{{ trans('administration.address_message_no_phone_addresses') }}</p>
                    <p class="large"><a href="{{ action('Administration\AddressController@create', ['type' => 'phone', 'parentId' => $parentId]) }}" class="ajax"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.address_heading_add_new_phone') }}</a></p>
                </div>
                </div>
            </div>
@else 
    @for ($i = 0; $i < count($addresses); $i++)
        @if ($i%2 == 0)
            <div class="row">
        @endif
                <div class="col-md-6">
                    @if ($addresses[$i]->primaryContact)
                    <div class="data-item primary">
                    @else
                    <div class="data-item">
                    @endif
                        <div class="data-header">
                            <h4>{{ trans('administration.address_label_'.$addresses[$i]->addressType.'_phone') }}</h4>
                            <span class="action">
                                   <a href="{{ action('Administration\AddressController@confirm', ['id' => $addresses[$i]->id, 'type' => 'phone']) }}" class="ajax">{{ trans('administration.common_delete') }}</a>
                                 | <a href="{{ action('Administration\AddressController@update', ['id' => $addresses[$i]->id, 'type' => 'phone']) }}" class="ajax">{{ trans('administration.common_update') }}</a>
                                @if (!$addresses[$i]->primaryContact)
                                 | <a href="{{ action('Administration\AddressController@makePrimary', ['id' => $addresses[$i]->id, 'type' => 'phone']) }}" class="ajax">{{ trans('administration.address_command_make_primary') }}</a>
                                @endif
                            </span>
                        </div>
                        <p class="h-adr">
                            <a href="tel:{{ $addresses[$i]->addressText }}">{{ $addresses[$i]->addressText }}</a>
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
                <div class="col-md-6 text-center">
                    <div class="data-item">
                        <a href="{{ action('Administration\AddressController@create', ['type' => 'phone', 'parentId' => $parentId]) }}" class="ajax large">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.address_heading_add_new_phone') }}
                        </a>    
                    </div>
                </div>
            </div>
@endif
        </div>
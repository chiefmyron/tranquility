        <div id="email-addresses-container">
            <h3>{{ trans('administration.address_heading_email_addresses') }}</h3>
@if (count($addresses) <= 0)
            <div class="data-item text-center">
                <p class="large">{{ trans('administration.address_message_no_email_addresses') }}</p>
                <p class="large">
                    <a href="{{ action('Administration\AddressController@create', ['type' => 'email', 'parentId' => $parentId]) }}" class="ajax" data-ajax-preload-target="modal">
                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.address_heading_add_new_email') }}
                    </a>
                </p>
            </div>
@else 
    @for ($i = 0; $i < count($addresses); $i++)
        @if ($addresses[$i]->primaryContact)
            <div class="data-item primary">
        @else
            <div class="data-item">
        @endif
                <div class="data-header">
                    <h4>{{ trans('administration.address_label_'.$addresses[$i]->addressType.'_email') }}</h4>
                    <span class="action">
                            <a href="{{ action('Administration\AddressController@confirm', ['id' => $addresses[$i]->id, 'type' => 'email']) }}" class="ajax" data-ajax-preload-target="modal">{{ trans('administration.common_delete') }}</a>
                            | <a href="{{ action('Administration\AddressController@update', ['id' => $addresses[$i]->id, 'type' => 'email']) }}" class="ajax" data-ajax-preload-target="modal">{{ trans('administration.common_update') }}</a>
                        @if (!$addresses[$i]->primaryContact)
                            | <a href="{{ action('Administration\AddressController@makePrimary', ['id' => $addresses[$i]->id, 'type' => 'email']) }}" class="ajax">{{ trans('administration.address_command_make_primary') }}</a>
                        @endif
                    </span>
                </div>
                <p>
                    <a href="mailto:{{ $addresses[$i]->addressText }}">{{ $addresses[$i]->addressText }}</a>
                </p>
            </div>
    @endfor

            <div class="text-center data-action">
                <a href="{{ action('Administration\AddressController@create', ['type' => 'email', 'parentId' => $parentId]) }}" class="ajax large" data-ajax-preload-target="modal">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> {{ trans('administration.address_heading_add_new_email') }}
                </a>    
            </div>
@endif
        </div>

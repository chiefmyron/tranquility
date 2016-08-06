            <div id="person-contact-details-container">
                @include('administration.addresses._partials.panels.physical-address', ['addresses' => $person->getAddresses('physical'), 'parentId' => $person->id])
                <hr />
                
                <div class="row">
                    <div class="col-md-6">
                        @include('administration.addresses._partials.panels.email-address', ['addresses' => $person->getAddresses('email'), 'parentId' => $person->id])
                        <hr />
                    </div>
                    <div class="col-md-6">
                        @include('administration.addresses._partials.panels.phone-address', ['addresses' => $person->getAddresses('phone'), 'parentId' => $person->id])
                        <hr />
                    </div>
                </div>
            </div>
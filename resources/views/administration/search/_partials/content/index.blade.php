<div class="row">
    <div class="col-lg-3 col-md-4 col-sm-4">
        @include('administration.accounts._partials.panels.show-details', ['account' => $account])
    </div>
    <div class="col-lg-9 col-md-8 col-sm-8">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#activity-feed" aria-controls="activity-feed" role="tab" data-toggle="tab">{{ trans('administration.common_activity_feed') }}</a></li>
            <li role="presentation"><a href="#contact-details" aria-controls="contact-details" role="tab" data-toggle="tab">{{ trans('administration.address_heading_contact_details') }}</a></li>
            <li role="presentation"><a href="#related-people" aria-controls="related-people" role="tab" data-toggle="tab">{{ trans('administration.people_heading_people') }}</a></li>
            <li role="presentation"><a href="#related-items" aria-controls="related-items" role="tab" data-toggle="tab">{{ trans('administration.common_related_items') }}</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane" id="activity-feed">...</div>
            <div role="tabpanel" class="tab-pane" id="contact-details">
                @include('administration.people._partials.panels.person-contact-details', ['person' => $account])
            </div>
            <div role="tabpanel" class="tab-pane" id="related-people">
                
            </div>
            <div role="tabpanel" class="tab-pane" id="related-items">...</div>
        </div>
    </div>
</div>
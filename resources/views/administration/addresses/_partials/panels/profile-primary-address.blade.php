<?php $primaryAddresses = $entity->getPrimaryAddresses(); ?>

@if (count($primaryAddresses) > 0)
<li>
    <div class="icon">
        <i class="icon-envelope"></i>
    </div>
    <div class="data-item">
        <span class="heading">{{ trans('administration.common_quick_contact') }}</span>
        @if (isset($primaryAddresses['email']))
        <p>
            <a href="mailto:{{ $primaryAddresses['email']->addressText }}">{{ $primaryAddresses['email']->addressText }}</a>
        </p>
        @endif
        @if (isset($primaryAddresses['phone']))
        <p class="h-adr">
            <a href="tel:{{ $primaryAddresses['phone']->addressText }}">{{ $primaryAddresses['phone']->addressText }}</a>
        </p>
        @endif
    </div>
</li>
@endif
<?php
$addressTypes = array(
    \Tranquility\Enums\BusinessObjects\Address\PhoneAddressTypes::Home => trans('administration.address_label_home_phone'),
    \Tranquility\Enums\BusinessObjects\Address\PhoneAddressTypes::Mobile => trans('administration.address_label_mobile_phone'),
    \Tranquility\Enums\BusinessObjects\Address\PhoneAddressTypes::Work => trans('administration.address_label_work_phone'),
    \Tranquility\Enums\BusinessObjects\Address\PhoneAddressTypes::Company => trans('administration.address_label_company_phone'),
    \Tranquility\Enums\BusinessObjects\Address\PhoneAddressTypes::Pager => trans('administration.address_label_pager_phone'),
    \Tranquility\Enums\BusinessObjects\Address\PhoneAddressTypes::Fax => trans('administration.address_label_fax_phone')
);
?>

<div class="form-group">
	{!! Form::label('addressType', trans('administration.address_label_address_type')) !!}
    {!! Form::select('addressType', $addressTypes, null, ['class' => 'form-control', 'autofocus']) !!}
	{!! FormError::inline('addressType', Session::get('messages')) !!}
</div>

<div class="form-group">
	{!! Form::label('addressText', trans('administration.address_label_phone_number')) !!}
	{!! Form::text('addressText', null, ['class' => 'form-control']) !!}	
	{!! FormError::inline('addressText', Session::get('messages')) !!}
</div>

{!! Form::hidden('type', \Tranquility\Enums\BusinessObjects\Address\AddressTypes::Phone) !!}
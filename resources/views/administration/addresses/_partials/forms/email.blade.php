<?php
$addressTypes = array(
    \Tranquility\Enums\BusinessObjects\Address\EmailAddressTypes::Personal => trans('administration.address_label_personal_email'),
    \Tranquility\Enums\BusinessObjects\Address\EmailAddressTypes::Work => trans('administration.address_label_work_email'),
    \Tranquility\Enums\BusinessObjects\Address\EmailAddressTypes::Other => trans('administration.address_label_other_email'),
);
?>

<div class="form-group">
	{!! Form::label('addressType', trans('administration.address_label_address_type')) !!}
    {!! Form::select('addressType', $addressTypes, null, ['class' => 'form-control', 'autofocus']) !!}
	{!! FormError::inline('addressType', Session::get('messages')) !!}
</div>

<div class="form-group">
	{!! Form::label('addressText', trans('administration.address_label_email_address')) !!}
	{!! Form::email('addressText', null, ['class' => 'form-control']) !!}	
	{!! FormError::inline('addressText', Session::get('messages')) !!}
</div>

{!! Form::hidden('category', \Tranquility\Enums\BusinessObjects\Address\AddressTypes::Email) !!}
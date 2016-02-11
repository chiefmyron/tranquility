<div class="form-group">
	{!! Form::label('addressLine1', trans('administration.address_label_line_1')) !!}
	{!! Form::text('addressLine1', null, ['class' => 'form-control', 'autofocus']) !!}	
	{!! FormError::inline('addressLine1', Session::get('messages')) !!}
</div>

<div class="form-group">
	{!! Form::label('addressLine2', trans('administration.address_label_line_2')) !!}
	{!! Form::text('addressLine2', null, ['class' => 'form-control', 'autofocus']) !!}	
	{!! FormError::inline('addressLine2', Session::get('messages')) !!}
</div>
	
<div class="form-group">
	{!! Form::label('city', trans('administration.address_label_city')) !!}
	{!! Form::text('city', null, ['class' => 'form-control']) !!}	
	{!! FormError::inline('city', Session::get('messages')) !!}
</div>	
	
<div class="form-group">
	{!! Form::label('state', trans('administration.address_label_state')) !!}
	{!! Form::text('state', null, ['class' => 'form-control']) !!}
	{!! FormError::inline('state', Session::get('messages')) !!}	
</div>	
	
<div class="form-group">
	{!! Form::label('postcode', trans('administration.address_label_postcode')) !!}
	{!! Form::text('postcode', null, ['class' => 'form-control']) !!}	
	{!! FormError::inline('postcode', Session::get('messages')) !!}
</div>

<div class="form-group">
	{!! Form::label('country', trans('administration.address_label_country')) !!}
    {!! Form::selectFromReferenceData('country', ['tableName' => 'cd_countries', 'translateCode' => true, 'translatePrefix' => 'countries.'], null, ['class' => 'form-control']) !!}
	{!! FormError::inline('country', Session::get('messages')) !!}	
</div>	